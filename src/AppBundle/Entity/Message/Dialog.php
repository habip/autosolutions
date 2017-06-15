<?php

namespace AppBundle\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Dialog
 *
 * @ORM\Table(name="DIALOG",
 *      uniqueConstraints={@UniqueConstraint(name="DIALOG_UNIQUE_KEY_IDX", columns={"UNIQUE_KEY"})},
 *      indexes={@Index(name="entity_lookup_idx", columns={"RELATED_ENTITY", "RELATED_ENTITY_ID"})})
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\DialogRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("uniqueKey")
 */
class Dialog
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
     * @var string
     *
     * @ORM\Column(name="RELATED_ENTITY", type="string", nullable=true)
     * @Assert\Expression(
     *      "null != this.getRelatedEntity() and null != this.getRelatedEntityId() or null == this.getRelatedEntity() and null == this.getRelatedEntityId()",
     *      message="If dialog is related to an entity both relatedEntity and relatedEntityId fields must not be null")
     */
    protected $relatedEntity;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="RELATED_ENTITY_ID", type="bigint", nullable=true)
     */
    protected $relatedEntityId;


    /**
     * Does dialog can be extended by adding new participants
     *
     * @var boolean
     *
     * @ORM\Column(name="EXTENSIBLE", type="boolean", nullable=true)
     */
    protected $extensible;

    /**
     * Ensures that not extensible dialogs with two participants wont be duplicated
     *
     * @var string
     *
     * @ORM\Column(name="UNIQUE_KEY", type="string", nullable=true, length=255)
     */
    protected $uniqueKey;

    /**
     * Participants of dialog
     *
     * @var array
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Message\DialogParticipant", mappedBy="dialog")
     * @Assert\Count(min="2", minMessage="You must specify at least two participants")
     */
    protected $participants;


    /**
     * Creator of dialog
     *
     * @var \AppBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="USER_ID", referencedColumnName="ID")
     * })
     * @Assert\NotNull()
     */
    protected $user;


    /**
     * Creation date and time
     *
     * @var \DateTime
     *
     * @ORM\Column(name="CREATION_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    protected $creationTimestamp;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\Message\Message", mappedBy="dialog")
     */
    protected $messages;

    /**
     * Number of state in which this Dialog was created or changed
     *
     * @var string
     *
     * @ORM\Column(name="STATE_ID", type="bigint", nullable=false)
     */
    protected $stateId = 0;

    /**
     * Last state number of this dialog
     *
     * @var integer
     *
     * @ORM\Column(name="LAST_STATE_ID", type="bigint", nullable=false)
     */
    protected $lastStateId = 0;

    /**
     * Last message sended in this dialog
     *
     * @var \AppBundle\Entity\Message\Message
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Message\Message")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LAST_MESSAGE_ID", referencedColumnName="ID")
     * })
     */
    protected $lastMessage;

    /**
     * Messages count in dialog
     *
     * @var integer
     *
     * @ORM\Column(name="MESSAGES_COUNT", type="integer", nullable=false)
     */
    protected $messagesCount = 0;

    private $currentUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set relatedEntity
     *
     * @param string $relatedEntity
     * @return Dialog
     */
    public function setRelatedEntity($relatedEntity)
    {
        $this->relatedEntity = $relatedEntity;

        return $this;
    }

    /**
     * Get relatedEntity
     *
     * @return string
     */
    public function getRelatedEntity()
    {
        return $this->relatedEntity;
    }

    /**
     * Set relatedEntityId
     *
     * @param string $relatedEntityId
     * @return Dialog
     */
    public function setRelatedEntityId($relatedEntityId)
    {
        $this->relatedEntityId = $relatedEntityId;

        return $this;
    }

    /**
     * Get relatedEntityId
     *
     * @return string
     */
    public function getRelatedEntityId()
    {
        return $this->relatedEntityId;
    }

    /**
     * Set creationTimestamp
     *
     * @param \DateTime $creationTimestamp
     * @return Dialog
     */
    public function setCreationTimestamp($creationTimestamp)
    {
        $this->creationTimestamp = $creationTimestamp;

        return $this;
    }

    /**
     * Get creationTimestamp
     *
     * @return \DateTime
     */
    public function getCreationTimestamp()
    {
        return $this->creationTimestamp;
    }

    /**
     * Add participants
     *
     * @param \AppBundle\Entity\Message\DialogParticipant $participants
     * @return Dialog
     */
    public function addParticipant(\AppBundle\Entity\Message\DialogParticipant $participants)
    {
        $this->participants[] = $participants;
        $participants->setDialog($this);

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \AppBundle\Entity\Message\DialogParticipant $participants
     */
    public function removeParticipant(\AppBundle\Entity\Message\DialogParticipant $participants)
    {
        $this->participants->removeElement($participants);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    public function isParticipant(User $user)
    {
        return $this->getParticipant($user) !== null;
    }

    /**
     *
     * @param \AppBundle\Enityt\User $user
     * @return \AppBundle\Entity\Message\DialogParticipant|NULL
     */
    public function getParticipant(\AppBundle\Entity\User $user)
    {
        foreach ($this->getParticipants() as $participant) {
            //Use id comparsion because after rollback entities may differ
            if ($user->getId() == $participant->getUser()->getId()) {
                return $participant;
            }
        }

        return null;
    }

    public function setCurrentUser(\AppBundle\Entity\User $user)
    {
        $this->currentUser = $user;
    }

    /**
     * Gets unread messages count in this dialog for current user
     */
    public function getUnreadCount()
    {
        if ($this->currentUser) {
            return $this->getParticipant($this->currentUser)->getUnreadCount();
        } else {
            return null;
        }
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Dialog
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
     * Set extensible
     *
     * @param boolean $extensible
     * @return Dialog
     */
    public function setExtensible($extensible)
    {
        $this->extensible = $extensible;

        return $this;
    }

    /**
     * Get extensible
     *
     * @return boolean
     */
    public function getExtensible()
    {
        return $this->extensible;
    }

    /**
     * Set uniqueKey
     *
     * @param string $uniqueKey
     * @return Dialog
     */
    public function setUniqueKey($uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;

        return $this;
    }

    /**
     * Get uniqueKey
     *
     * @return string
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }

    /**
     * Set stateId
     *
     * @param integer $stateId
     * @return Dialog
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
     * Set lastStateId
     *
     * @param integer $lastStateId
     * @return Dialog
     */
    public function setLastStateId($lastStateId)
    {
        $this->lastStateId = $lastStateId;

        return $this;
    }

    /**
     * Get lastStateId
     *
     * @return integer
     */
    public function getLastStateId()
    {
        return $this->lastStateId;
    }

    /**
     * Add messages
     *
     * @param \AppBundle\Entity\Message\Message $messages
     * @return Dialog
     */
    public function addMessage(\AppBundle\Entity\Message\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \AppBundle\Entity\Message\Message $messages
     */
    public function removeMessage(\AppBundle\Entity\Message\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        if ($this->participants->count() == 2) {
            $ids = array();
            foreach ($this->participants as $participant) {
                $ids[] = $participant->getUser()->getId();
            }
            sort($ids);

            if ($this->relatedEntity && $this->relatedEntityId) {
                $this->uniqueKey = sprintf('dialog_%s_%s_%s', $this->relatedEntity, $this->relatedEntityId , implode('_', $ids));
            } else {
                $this->uniqueKey = sprintf('dialog_%s', implode('_', $ids));
            }
        }
    }

    /**
     * Set messagesCount
     *
     * @param integer $messagesCount
     * @return Dialog
     */
    public function setMessagesCount($messagesCount)
    {
        $this->messagesCount = $messagesCount;

        return $this;
    }

    /**
     * Get messagesCount
     *
     * @return integer
     */
    public function getMessagesCount()
    {
        return $this->messagesCount;
    }

    /**
     * Set lastMessage
     *
     * @param \AppBundle\Entity\Message\Message $lastMessage
     * @return Dialog
     */
    public function setLastMessage(\AppBundle\Entity\Message\Message $lastMessage = null)
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * Get lastMessage
     *
     * @return \AppBundle\Entity\Message\Message
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }
}
