<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\DBAL\LockMode;

/**
 * Message
 *
 * @ORM\Table(name="MESSAGES")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class Message
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
     *
     * @var string
     *
     * @ORM\Column(name="GUID", type="guid", nullable=false)
     * @Assert\NotNull
     */
    private $guid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $createdTimestamp;

    /**
     * Message author
     *
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="USER_ID", referencedColumnName="ID")
     * })
     * @Assert\NotNull()
     */
    private $user;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Message\MessageStatus", mappedBy="message")
     */
    private $statuses;

    /**
     *
     * @var \AppBundle\Entity\Message\Dialog
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Message\Dialog", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="DIALOG_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    private $dialog;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="BODY", type="text", nullable=false)
     */
    private $body;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Message\Attachment", mappedBy="message", cascade={"persist"})
     */
    private $attachments;

    /**
     * Number of state in which this message was created or changed
     *
     * @var string
     *
     * @ORM\Column(name="STATE_ID", type="bigint", nullable=false)
     */
    private $stateId = 0;

    /**
     *
     * Author of message. It can differ from user in case of company when some operator send it.
     *
     * @var string
     *
     * @ORM\Column(name="AUTHOR", type="string", nullable=true)
     */
    private $author;

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
     * Get guid
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set guid
     *
     * @return Message
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
        return $this;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set user - author of this message
     *
     * @param \AppBundle\Entity\User $user
     * @return Message
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user - author of this message
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set dialog
     *
     * @param \AppBundle\Entity\Message\Dialog $dialog
     * @return Message
     */
    public function setDialog(\AppBundle\Entity\Message\Dialog $dialog = null)
    {
        $this->dialog = $dialog;

        return $this;
    }

    /**
     * Get dialog
     *
     * @return \AppBundle\Entity\Message\Dialog
     */
    public function getDialog()
    {
        return $this->dialog;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdTimestamp = new \DateTime();
        $this->statuses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set createdTimestamp
     *
     * @param \DateTime $createdTimestamp
     * @return Message
     */
    public function setCreatedTimestamp($createdTimestamp)
    {
        $this->createdTimestamp = $createdTimestamp;

        return $this;
    }

    /**
     * Get createdTimestamp
     *
     * @return \DateTime
     */
    public function getCreatedTimestamp()
    {
        return $this->createdTimestamp;
    }

    /**
     * Add status
     *
     * @param \AppBundle\Entity\Message\MessageStatus $status
     * @return Message
     */
    public function addStatus(\AppBundle\Entity\Message\MessageStatus $status)
    {
        $this->statuses[] = $status;
        $status->setMessage($this);

        return $this;
    }

    /**
     * Remove statuses
     *
     * @param \AppBundle\Entity\Message\MessageStatus $statuses
     */
    public function removeStatus(\AppBundle\Entity\Message\MessageStatus $statuses)
    {
        $this->statuses->removeElement($statuses);
    }

    /**
     * Get statuses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * Get users status
     */
    public function getStatusForUser(User $user)
    {
        foreach ($this->statuses as $status) {
            if ($status->getUser() == $user) {
                return $status;
            }
        }

        return null;
    }

    /**
     * Set stateId
     *
     * @param integer $stateId
     * @return Message
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Add attachment
     *
     * @param \AppBundle\Entity\Message\Attachment $attachment
     * @return Message
     */
    public function addAttachment(\AppBundle\Entity\Message\Attachment $attachment)
    {
        $this->attachments[] = $attachment;
        $attachment->setMessage($this);

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param \AppBundle\Entity\Message\Attachment $attachment
     */
    public function removeAttachment(\AppBundle\Entity\Message\Attachment $attachment)
    {
        $this->attachments->removeElement($attachment);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Get stateId
     *
     * @return integer
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set author
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     *
     * @param LifecycleEventArgs $event
     *
     * @ORM\PrePersist
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $dialog = $em->find('AppBundleMessage:Dialog', $this->dialog->getId(), LockMode::PESSIMISTIC_WRITE);
        $dialog->setMessagesCount($dialog->getMessagesCount()+1);
        $dialog->setLastMessage($this);
    }
}
