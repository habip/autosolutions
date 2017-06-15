<?php

namespace AppBundle\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Notification
 *
 * @ORM\Table(name="NOTIFICATIONS")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\NotificationRepository")
 *
 */
class Notification
{

    const TYPE_SINGLE_CHOICE = 'single';
    const TYPE_MULTIPLE_CHOICE = 'multiple';

    const STATUS_NEW = 'new';
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
     * Notification recipient
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
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $createdTimestamp;

    /**
     *
     * @var \AppBundle\Entity\CarOwnerRequest
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CarOwnerRequest")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="REQUEST_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    private $request;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE", type="string", length=8, columnDefinition="enum('single', 'multiple') NOT NULL DEFAULT 'single'")
     */
    private $type = self::TYPE_SINGLE_CHOICE;

    /**
     * Key value array in form:
     * {
     *   "one" => "choose one",
     *   "two" => "choose two"
     * }
     *
     * @var array
     *
     * @ORM\Column(name="ACTIONS", type="json_array")
     */
    private $actions;

    /**
     * Key value array in form:
     * {
     *   "one" => "choose one",
     *   "two" => "choose two"
     * }
     *
     * @var array
     *
     * @ORM\Column(name="CHOICES", type="json_array")
     */
    private $choices;
    
    /*
    silent;            //не привлекает пользователя (обычное оповещение)
    no_clear      //нельзя скрыть пока оповещение скроеться само (на будущее)
    show_info   //показывает модальное окно
    */
    /**
     * Key array in form:
     * [
     *   "alert"
     * ]
     *
     * @var array
     *
     * @ORM\Column(name="FLAGS", type="json_array")
     */
    private $flags;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="MESSAGE", type="text", nullable=false)
     */
    private  $message;

    /**
     *
     * Author of notification. It can differ from user in case of company when some operator send it.
     *
     * @var string
     *
     * @ORM\Column(name="AUTHOR", type="string", nullable=true)
     */
    private $author;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="CURRENT_STATUS", type="string", length=50, nullable=false, columnDefinition="enum('new', 'read') NOT NULL DEFAULT 'new'")
     */
    private $status = self::STATUS_NEW;

    public function __construct()
    {
        $this->createdTimestamp = new \DateTime();
        $this->actions = array();
        $this->choices = array();
        $this->flags = array();
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
     * Set user - recipient of this notifaction
     *
     * @param \AppBundle\Entity\User $user
     * @return Notification
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user - recipient of this notifaction
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
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
     * Set type
     *
     * @param string $type
     *
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set actions
     *
     * @param array $actions
     *
     * @return Notification
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     *
     * @param string $key
     * @param string $value
     * @return \AppBundle\Entity\Notification\Notification
     */
    public function addAction($key, $value)
    {
        $this->actions[$key] = $value;

        return $this;
    }

    /**
     * Get actions
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set choises
     *
     * @param array $choices
     *
     * @return Notification
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Get choises
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     *
     * @param string $key
     * @param string $value
     * @return \AppBundle\Entity\Notification\Notification
     */
    public function addChoise($key, $value)
    {
        $this->choices[$key] = $value;

        return $this;
    }

    /**
     *
     * Set message
     *
     * @param string $message
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return \AppBundle\Entity\Notification\Notification
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
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
     * Set request
     *
     * @param \AppBundle\Entity\CarOwnerRequest $request
     *
     * @return Notification
     */
    public function setRequest(\AppBundle\Entity\CarOwnerRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return \AppBundle\Entity\CarOwnerRequest
     */
    public function getRequest()
    {
        return $this->request;
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
     * Set flags
     *
     * @param array $flags
     *
     * @return Notification
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     *
     * @param string $flag
     * @return \AppBundle\Entity\Notification\Notification
     */
    public function addFlag($flag)
    {
        $this->flags[] = $flag;

        return $this;
    }

    /**
     * Get flags
     *
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }
}
