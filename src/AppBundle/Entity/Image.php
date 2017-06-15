<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

/**
 * Image
 *
 * @ORM\Table(name="IMAGES")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 */
class Image
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Thumb", mappedBy="image")
     */
    private $thumbs;

    /**
     *
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="USER_ID", referencedColumnName="ID")
     * })
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ADDED_TIMESTAMP", type="datetime", nullable=true, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $addedTimestamp;

    /**
     *
     * @var string
     */
    private $thumb100x100;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->thumbs = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Image
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
     * @return Image
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
     * Set user
     *
     * @param AppBundle\Entity\User $user
     * @return Image
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set addedTimestamp
     *
     * @param \DateTime $addedTimestamp
     * @return Image
     */
    public function setAddedTimestamp($addedTimestamp)
    {
        $this->addedTimestamp = $addedTimestamp;

        return $this;
    }

    /**
     * Get addedTimestamp
     *
     * @return \DateTime
     */
    public function getAddedTimestamp()
    {
        return $this->addedTimestamp;
    }

    /**
     * Add thumbs
     *
     * @param \AppBundle\Entity\Thumb $thumbs
     * @return Image
     */
    public function addThumb(\AppBundle\Entity\Thumb $thumbs)
    {
        $this->thumbs[] = $thumbs;

        return $this;
    }

    /**
     * Remove thumbs
     *
     * @param \AppBundle\Entity\Thumb $thumbs
     */
    public function removeThumb(\AppBundle\Entity\Thumb $thumbs)
    {
        $this->thumbs->removeElement($thumbs);
    }

    /**
     * Get thumbs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getThumbs()
    {
        return $this->thumbs;
    }

    /**
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $crop
     * @return \AppBundle\Entity\Thumb
     */
    private function getThumb($width, $height, $crop)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                    Criteria::expr()->eq('height', $width),
                    Criteria::expr()->eq('width', $height),
                    Criteria::expr()->eq('isCropped', $crop)
            ));

        $thumbs = $this->thumbs->matching($criteria);

        if ($thumbs->count() > 0) {
            return $thumbs[0];
        } else {
            return null;
        }
    }

    public function getThumb100x100()
    {
        /*if (null === $this->thumb100x100) {
            $thumb = $this->getThumb(100, 100, true);

            if (null !== $thumb) {
                $this->thumb100x100 = $thumb->getUrl();
            }
        }*/

        return $this->thumb100x100;
    }
}
