<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message attachment
 *
 * @ORM\Table(name="MESSAGE_ATTACHMENT")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="TYPE", type="string")
 * @ORM\DiscriminatorMap({"photo" = "ImageAttachment"})
 *
 */
abstract class Attachment
{

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @var \AppBundle\Entity\Message\Message
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Message\Message", inversedBy="attachments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="MESSAGE_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    protected $message;

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
     * Set message
     *
     * @param \AppBundle\Entity\Message\Message $message
     * @return Attachment
     */
    public function setMessage(\AppBundle\Entity\Message\Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \AppBundle\Entity\Message\Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
