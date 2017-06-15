<?php
namespace AppBundle\Utils;

use Gregwar\ImageBundle\Services\ImageHandling;
use AppBundle\Entity\Image;
use AppBundle\Entity\Thumb;
use Doctrine;

class ThumbManager
{
    private $imageHandling;

    public function __construct(ImageHandling $imageHandling)
    {
        $this->imageHandling = $imageHandling;
    }

    public function createThumb(Image $image, $width = null, $height = null, $crop = false, $em = null)
    {
        $img = $this->imageHandling->open($image->getPath());

        $w = $img->width();
        $h = $img->height();
        if ($width && $height) {
            $sizeFolder = $width . 'x' .$height;
        } else if ($width) {
            $sizeFolder = 'w' . $width;
        } else if ($height) {
            $sizeFolder = 'h' . $height;
        } else {
            throw new \Exception("one of dimensions must be defined for resize");
        }
        $filePath = $image->getPath();
        $fileUrl = $image->getUrl();
        $thumbPath = sprintf('%s/%s/%s', dirname($filePath), $sizeFolder, basename($filePath));
        $thumbUrl = sprintf('%s/%s/%s', dirname($fileUrl), $sizeFolder, basename($fileUrl));
        if ($width && $height && $crop) {
            $imgSrc = $img->zoomCrop($width, $height, 0xdddddd)->get();
        } else {
            $imgSrc = $img->scaleResize($width, $height, 0xdddddd)->get();
        }
        $url = parse_url($thumbPath);
        if (!(isset($url['scheme']) && $url['scheme'])) {
            $d = dirname($thumbPath);
            if (!file_exists($d)) {
                mkdir($d);
            }
        }
        file_put_contents($thumbPath, $imgSrc);
        $it = new Thumb();
        $it->setImage($em && $image->getId() ? $em->getReference('AppBundle:Image', $image->getId()) : $image);
        $it->setPath($thumbPath);
        $it->setUrl($thumbUrl);
        $it->setWidth($tw = $img->width());
        $it->setHeight($th = $img->height());
        $it->setIsCropped($width && $height && $crop);
        $it->setIsAspectRatioPreserved(!$width || !$height);
        return $it;
    }
}