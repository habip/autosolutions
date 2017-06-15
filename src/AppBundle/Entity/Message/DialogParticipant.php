<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Dialog Participant
 *
 * @ORM\Table(name="DIALOG_PARTICIPANT")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\DialogParticipantRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity({"dialog", "user"})
 */
class DialogParticipant
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
     * @var \AppBundle\Entity\Message\Dialog
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Message\Dialog", inversedBy="participants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="DIALOG_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    private $dialog;


    /**
     * Dialog participant
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
     * Unread messages count in this dialog
     *
     * @var integer
     *
     * @ORM\Column(name="UNREAD_COUNT", type="integer", nullable=false)
     */
    private $unreadCount = 0;

    /**
     * Last read message id in this dialog
     *
     * @var integer
     *
     * @ORM\Column(name="LAST_READ_MESSAGE_ID", type="integer", nullable=false)
     */
    private $lastReadMessageId = 0;

    /**
     * Number of state in which this DialogParticipant was created or changed
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
     * Set unreadCount
     *
     * @param integer $unreadCount
     * @return DialogParticipant
     */
    public function setUnreadCount($unreadCount)
    {
        $this->unreadCount = $unreadCount;

        return $this;
    }

    /**
     * Get unreadCount
     *
     * @return integer
     */
    public function getUnreadCount()
    {
        return $this->unreadCount;
    }

    /**
     * Set lastReadMessageId
     *
     * @param integer $lastReadMessageId
     * @return DialogParticipant
     */
    public function setLastReadMessageId($lastReadMessageId)
    {
        $this->lastReadMessageId = $lastReadMessageId;

        return $this;
    }

    /**
     * Get lastReadMessageId
     *
     * @return integer
     */
    public function getLastReadMessageId()
    {
        return $this->lastReadMessageId;
    }

    /**
     * Set dialog
     *
     * @param \AppBundle\Entity\Message\Dialog $dialog
     * @return DialogParticipant
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return DialogParticipant
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
     * Set stateId
     *
     * @param integer $stateId
     * @return DialogParticipant
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
     * @ORM\PrePersist
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();

        $this->stateId = $this->dialog->getLastStateId();
    }
}
