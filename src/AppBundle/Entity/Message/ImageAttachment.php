<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Image attachment
 *
 * @ORM\Table(name="IMAGE_ATTACHMENT")
 * @ORM\Entity()
 *
 */
class ImageAttachment extends Attachment
{
    /**
     *
     * @var \AppBundle\Entity\Image
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IMAGE_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    private $image;

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     * @return ImageAttachment
     */
    public function setImage(\AppBundle\Entity\Image $image)
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

}
