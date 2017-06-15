<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\DBAL\LockMode;

/**
 * Message
 *
 * @ORM\Table(name="MESSAGE_STATUS")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\MessageStatusRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class MessageStatus
{

    const STATUS_NEW = 'new';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Status owner
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
     * @var \AppBundle\Entiyt\Message\Message
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Message\Message", inversedBy="statuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="MESSAGE_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DELIVERED_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NULL")
     */
    private $deliveredTimestamp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="READ_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NULL")
     */
    private $readTimestamp;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="CURRENT_STATUS", type="string", length=50, nullable=false, columnDefinition="enum('new', 'delivered', 'read') NOT NULL DEFAULT 'new'")
     */
    private $status = self::STATUS_NEW;

    /**
     * Number of state in which this MessageStatus was created or changed
     *
     * @var string
     *
     * @ORM\Column(name="STATE_ID", type="bigint", nullable=false)
     */
    private $stateId = 0;

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
     * Set deliveredTimestamp
     *
     * @param \DateTime $deliveredTimestamp
     * @return MessageStatus
     */
    public function setDeliveredTimestamp($deliveredTimestamp)
    {
        $this->deliveredTimestamp = $deliveredTimestamp;

        return $this;
    }

    /**
     * Get deliveredTimestamp
     *
     * @return \DateTime
     */
    public function getDeliveredTimestamp()
    {
        return $this->deliveredTimestamp;
    }

    /**
     * Set readTimestamp
     *
     * @param \DateTime $readTimestamp
     * @return MessageStatus
     */
    public function setReadTimestamp($readTimestamp)
    {
        $this->readTimestamp = $readTimestamp;

        return $this;
    }

    /**
     * Get readTimestamp
     *
     * @return \DateTime
     */
    public function getReadTimestamp()
    {
        return $this->readTimestamp;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return MessageStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return MessageStatus
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set message
     *
     * @param \AppBundle\Entity\Message\Message $message
     * @return MessageStatus
     */
    public function setMessage(\AppBundle\Entity\Message\Message $message = null)
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

    /**
     * Set stateId
     *
     * @param integer $stateId
     * @return MessageStatus
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
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
     *
     * @param LifecycleEventArgs $event
     *
     * @ORM\PrePersist
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        if ($this->status == self::STATUS_NEW) {
            $em->lock($this->getMessage()->getDialog()->getParticipant($this->user), LockMode::PESSIMISTIC_WRITE);

            $em->createQuery('
                    update AppBundleMessage:DialogParticipant p
                    set p.unreadCount = p.unreadCount + 1
                    where identity(p.dialog) = :dialog and identity(p.user) = :user')
                        ->setParameter('dialog', $this->message->getDialog()->getId())
                        ->setParameter('user', $this->user->getId())
                        ->execute();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $em = $event->getEntityManager();
        if ($event->hasChangedField('status')) {
            $oldStatus = $event->getOldValue('status');
            $status    = $event->getNewValue('status');
            if ($oldStatus == self::STATUS_NEW && ($status == self::STATUS_DELIVERED || $status == self::STATUS_READ)) {
                $this->deliveredTimestamp = new \DateTime();
            }
            if (($oldStatus == self::STATUS_NEW || $oldStatus == self::STATUS_DELIVERED) && $status == self::STATUS_READ) {
                $this->readTimestamp = new \DateTime();

                $em->lock($this->getMessage()->getDialog()->getParticipant($this->user), LockMode::PESSIMISTIC_WRITE);

                $em->createQuery('
                        update AppBundleMessage:DialogParticipant p
                        set p.unreadCount = p.unreadCount - 1
                        where identity(p.dialog) = :dialog and identity(p.user) = :user')
                    ->setParameter('dialog', $this->message->getDialog()->getId())
                    ->setParameter('user', $this->user->getId())
                    ->execute();
            }
        }
    }
}
