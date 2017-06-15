<?php
namespace AppBundle\Extensions;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Image;
use Doctrine\ORM\Query;

class ThumbnailTwig extends \Twig_Extension
{
    private $container;
    private $environment;
    private $em;

    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->container = $container;
        $this->em = $entityManager;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
    public function getFunctions()
    {
        return array(
            'thumb' => new \Twig_Function_Method($this, 'thumbnail', array('is_safe' => array('html'))),
        );
    }
    public function thumbnail($image, $width = null, $height = null, $crop = false)
    {
        $isArray = is_array($image);
        $thumbs = $isArray ? $image['thumbs'] : $image->getThumbs();
        $imageId = $isArray ? $image['id'] : $image->getId();
        if ($thumb = $this->_findThumb($imageId, $thumbs, $isArray, $width, $height, $crop)) {
            return $isArray ? $thumb['url'] : $thumb->getUrl();
        } else {

            $pt = $this->container->get('app.thumb_manager')->createThumb(
                $isArray ? $this->em->getRepository('AppBundle:Image')->find($image['id']) : $image,
                $width,
                $height,
                $crop,
                $this->em
            );

            $this->em->persist($pt);
            $this->em->flush();

            return $pt->getUrl();
        }
    }

    private function _findThumb($imageId, $thumbs, $isArray, $width, $height, $crop = false)
    {
        foreach ($thumbs as $thumb) {
            $w = $isArray ? $thumb['width'] : $thumb->getWidth();
            $h = $isArray ? $thumb['height'] : $thumb->getHeight();
            $c = (bool)($isArray ? $thumb['isCropped'] : $thumb->getIsCropped());
            $a = (bool)($isArray ? $thumb['isAspectRatioPreserved'] : $thumb->getIsAspectRatioPreserved());

            if ((!$width && $height && $h == $height && !$c && $a)
                || ($width && !$height && $w == $width && !$c && $a)
                || ($width && $height && $width == $w && $height == $h && $c == $crop && !$a)) {
                return $thumb;
            }
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('t')
            ->from('AppBundle:Thumb', 't')
            ->where('identity(t.image) = :image and t.isCropped = :cropped and t.isAspectRatioPreserved = :arp')
            ->setParameter('image', $imageId)
            ->setParameter('cropped', $crop ? 1: 0)
            ->setParameter('arp', $width && $height ? 0: 1);

        if ($width) {
            $qb->andWhere('t.width = :width')
                ->setParameter('width', $width);
        }
        if ($height) {
            $qb->andWhere('t.height = :height')
                ->setParameter('height', $height);
        }
        $t = $qb->getQuery()->getResult($isArray ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT);

        if (sizeof($t) > 0) {
            return $t[0];
        }

        return null;
    }
    public function getName()
    {
        return 'thumbnail';
    }
}