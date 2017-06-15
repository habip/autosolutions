<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use AppBundle\Entity\CarOwner;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\Image;

class ImageRepository extends EntityRepository
{

    /**
     *
     * Removing image and its thumbnails
     *
     * TODO: remove images physically from disk, but we must do it after transaction is commited. So maybe do it in lifecycle event subscriber.
     *
     * @param Image $image
     */
    public function remove(Image $image)
    {
        $thumbs = $this->_em
            ->createQuery('select t from AppBundle:Thumb t where identity(t.image) = :image')
            ->setParameter('image', $image->getId())
            ->getResult();

        foreach ($thumbs as $thumb) {
            $this->_em->remove($thumb);
        }

        $this->_em->remove($image);
    }

}