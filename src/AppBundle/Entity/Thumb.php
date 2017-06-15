<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thumb
 *
 * @ORM\Table(name="THUMBS")
 * @ORM\Entity
 */
class Thumb
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="PATH", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="URL", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="WIDTH", type="integer", nullable=false)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="HEIGHT", type="integer", nullable=false)
     */
    private $height;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_CROPPED", type="boolean", nullable=false)
     */
    private $isCropped;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_ASPECT_RATIO_PRESERVED", type="boolean", nullable=false)
     */
    private $isAspectRatioPreserved;

    /**
     * @var AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", inversedBy="thumbs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IMAGE_ID", referencedColumnName="ID")
     * })
     */
    private $image;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Thumb
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Thumb
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Thumb
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Thumb
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set isCropped
     *
     * @param boolean $isCropped
     * @return Thumb
     */
    public function setIsCropped($isCropped)
    {
        $this->isCropped = $isCropped;

        return $this;
    }

    /**
     * Get isCropped
     *
     * @return boolean
     */
    public function getIsCropped()
    {
        return $this->isCropped;
    }

    /**
     * Set isAspectRatioPreserved
     *
     * @param boolean $isAspectRatioPreserved
     * @return Thumb
     */
    public function setIsAspectRatioPreserved($isAspectRatioPreserved)
    {
        $this->isAspectRatioPreserved = $isAspectRatioPreserved;

        return $this;
    }

    /**
     * Get isAspectRatioPreserved
     *
     * @return boolean
     */
    public function getIsAspectRatioPreserved()
    {
        return $this->isAspectRatioPreserved;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     * @return Thumb
     */
    public function setPhoto(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     * @return Thumb
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }
}
