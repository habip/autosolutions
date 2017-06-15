<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Change
 *
 * @ORM\Table(name="CHANGES")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\ChangeRepository")
 */
class Change
{

    /**
     * Create action
     */
    const ACTION_CREATE = 'create';

    /**
     * Update action
     */
    const ACTION_UPDATE = 'update';

    /**
     * Remove action
     */
    const ACTION_REMOVE = 'remove';

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="GUID", type="guid")
     */
    private $guid;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="CHANGE_ACTION", type="string", nullable=false, columnDefinition="enum('create','update','remove') NOT NULL")
     */
    private $action;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="OBJECT_CLASS", type="string", length=255, nullable=false)
     */
    private $objectClass;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="OBJECT_ID", type="bigint", nullable=false)
     */
    private $objectId;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="CHANGE_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $timestamp;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="CHANGE_VALUE", type="text", nullable=false)
     */
    private $value;

    /**
     *
     * @var array
     *
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ChangeSubscriber", mappedBy="change", cascade={"persist"})
     */
    private $subscribers;

    public function __construct()
    {
        $this->guid = $this->generateGUID();
        $this->subscribers = new ArrayCollection();
    }

    private function generateGUID()
    {
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
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
     * Set guid
     *
     * @param guid $guid
     *
     * @return Change
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid
     *
     * @return guid
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set objectClass
     *
     * @param string $objectClass
     * @return Change
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * Get objectClass
     *
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     * @return Change
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return Change
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Change
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add subscriber
     *
     * @param \AppBundle\Entity\ChangeSubscriber $subscriber
     * @return Change
     */
    public function addSubscriber(\AppBundle\Entity\ChangeSubscriber $subscriber)
    {
        $this->subscribers[] = $subscriber;
        $subscriber->setChange($this);

        return $this;
    }

    /**
     * Remove subscriber
     *
     * @param \AppBundle\Entity\ChangeSubscriber $subscriber
     */
    public function removeSubscriber(\AppBundle\Entity\ChangeSubscriber $subscriber)
    {
        $this->subscribers->removeElement($subscriber);
    }

    /**
     * Get subscribers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * Get array of subscribers user ids
     *
     * @return array
     */
    public function getSubscribersIds()
    {
        $result = array();

        foreach ($this->subscribers as $subscriber) {
            $result[] = $subscriber->getUser()->getId();
        }

        return $result;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Change
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
