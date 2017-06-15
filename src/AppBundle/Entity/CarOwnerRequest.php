<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Notification\Notification;
use AppBundle\Validator\Constraints as AppAssert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;


/**
 *
 * CarService
 *
 * @ORM\Table(name="CAR_OWNER_REQUESTS")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CarOwnerRequestRepository")
 * @AppAssert\Service(name="app.validator.car_owner_request")
 */
class CarOwnerRequest
{

    const STATUS_NEW = 'new';
    const STATUS_INPROGRESS = 'inprogress';
    const STATUS_ASSIGN = 'assign';
    const STATUS_REASSIGN = 'reassign';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELED = 'canceled';
    const STATUS_TIMEOUT = 'timeout';
    const STATUS_DONE = 'done';
    const STATUS_POSTPONED = 'postponed';

    public static $statusNames = array(
            self::STATUS_NEW => 'новый',
            self::STATUS_INPROGRESS => 'в работе',
            self::STATUS_ASSIGN => 'назначено',
            self::STATUS_REASSIGN => 'переназначено',
            self::STATUS_REJECTED => 'отказано',
            self::STATUS_CANCELED => 'отменено',
            self::STATUS_TIMEOUT => 'отменено по времени',
            self::STATUS_DONE => 'завершено',
            self::STATUS_POSTPONED => 'отложено',
    );

    /**
     *
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
     * @ORM\Column(name="STATUS", type="string", length=15, nullable=false, columnDefinition="enum('new', 'inprogress', 'assign', 'reassign', 'rejected', 'canceled', 'timeout', 'done', 'postponed') NOT NULL DEFAULT 'new'")
     */
    private $status = self::STATUS_NEW;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * Car owner desired date and time
     *
     * @var string
     *
     * @ORM\Column(name="CAR_OWNER_DATE", type="datetime", nullable=true)
     * @Assert\NotNull(message="Выберите дату записи", groups={"request"})
     */
    private $carOwnerDate;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="CAR_OWNER_TIME_PERIOD", type="bigint", nullable=true)
     */
    private $carOwnerTimePeriod;

    /**
     * @var string
     *
     * @ORM\Column(name="TIME", type="datetime", nullable=true)
     */
    private $time;


    /**
     * @var string
     *
     * @ORM\Column(name="PHONE", type="string", length=50, nullable=true)
     * @AssertPhoneNumber(type="mobile")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="text", nullable=true)
     */
    private $description;

    /**
     *
     * @var \AppBundle\Entity\Car
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car", inversedBy="carOwnerRequests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    private $car;


    /**
     *
     * @var \AppBundle\Entity\CarService
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarService", inversedBy="carOwnerRequests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    private $carService;

    /**
     *
     * @var \AppBundle\Entity\CarOwner
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarOwner", inversedBy="carOwnerRequests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_OWNER_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    private $carOwner;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Service")
     * @ORM\JoinTable(name="CAR_OWNER_SERVICES",
     *     joinColumns={@ORM\JoinColumn(name="CAR_OWNER_REQUEST_SERVICE_ID", referencedColumnName="ID")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="SERVICE_ID", referencedColumnName="ID")}
     * )
     **/
    private $services;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ServiceReason")
     * @ORM\JoinTable(name="CAR_OWNER_REQUEST_REASONS",
     *     joinColumns={@ORM\JoinColumn(name="CAR_OWNER_REQUEST_ID", referencedColumnName="ID")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="REASON_ID", referencedColumnName="ID")}
     * )
     **/
    private $reasons;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="DIALOG_ID", type="bigint", nullable=true)
     */
    private $dialogId;

    /**
     *
     * @var \AppBundle\Entity\Review
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\Review", inversedBy="carOwnerRequest")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="REVIEW_ID", referencedColumnName="ID")
     * })
     */
    private $review;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ADDED_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $addedTimestamp;

    /**
     * Actual time of checkin (set by car service)
     *
     * @var \DateTime
     *
     * @ORM\Column(name="CHECK_IN_DATE_TIME", type="datetime", nullable=true)
     */
    private $checkInDateTime;

    /**
     * Actual time of checkout (set by car service)
     *
     * @var \DateTime
     *
     * @ORM\Column(name="CHECK_OUT_DATE_TIME", type="datetime", nullable=true)
     */
    private $checkOutDateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="MASTER_INSPECTOR", type="string", length=100, nullable=true)
     */
    private $masterInspector;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CarOwnerRequestItem", mappedBy="request")
     */
    private $items;

    private $workItems;

    private $partItems;

    /**
     *
     * @var CarServicePost
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarServicePost")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_SERVICE_POST_ID", referencedColumnName="ID")
     * })
     */
    private $post;

    /**
     * Estimated time of arrival based on schedule
     *
     * @var \DateTime
     *
     * @ORM\Column(name="ENTRY_TIME", type="datetime", nullable=true)
     */
    private $entryTime;

    /**
     * Estimated time of completion of the works, taking into account the schedule
     *
     * @var \DateTime
     *
     * @ORM\Column(name="EXIT_TIME", type="datetime", nullable=true)
     */
    private $exitTime;

    /**
     *
     * @var CarServiceSchedule
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CarServiceSchedule", mappedBy="carOwnerRequest")
     */
    private $schedule;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addedTimestamp = new \DateTime();
        $this->services = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reasons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set status
     *
     * @param string $status
     *
     * @return CarOwnerRequest
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
     * Set name
     *
     * @param string $name
     *
     * @return CarOwnerRequest
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set carOwnerDate
     *
     * @param \DateTime $carOwnerDate
     *
     * @return CarOwnerRequest
     */
    public function setCarOwnerDate($carOwnerDate)
    {
        $this->carOwnerDate = $carOwnerDate;

        return $this;
    }

    /**
     * Get carOwnerDate
     *
     * @return \DateTime
     */
    public function getCarOwnerDate()
    {
        return $this->entryTime!=null ? $this->entryTime : $this->carOwnerDate;
    }

    /**
     * Set carOwnerTimePeriod
     *
     * @param integer $carOwnerTimePeriod
     *
     * @return CarOwnerRequest
     */
    public function setCarOwnerTimePeriod($carOwnerTimePeriod)
    {
        $this->carOwnerTimePeriod = $carOwnerTimePeriod;

        return $this;
    }

    /**
     * Get carOwnerTimePeriod
     *
     * @return integer
     */
    public function getCarOwnerTimePeriod()
    {
        return $this->carOwnerTimePeriod;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return CarOwnerRequest
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return CarOwnerRequest
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return CarOwnerRequest
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return CarOwnerRequest
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dialogId
     *
     * @param integer $dialogId
     *
     * @return CarOwnerRequest
     */
    public function setDialogId($dialogId)
    {
        $this->dialogId = $dialogId;

        return $this;
    }

    /**
     * Get dialogId
     *
     * @return integer
     */
    public function getDialogId()
    {
        return $this->dialogId;
    }

    /**
     * Set addedTimestamp
     *
     * @param \DateTime $addedTimestamp
     *
     * @return CarOwnerRequest
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
     * Set car
     *
     * @param \AppBundle\Entity\Car $car
     *
     * @return CarOwnerRequest
     */
    public function setCar(\AppBundle\Entity\Car $car)
    {
        $this->car = $car;

        return $this;
    }

    /**
     * Get car
     *
     * @return \AppBundle\Entity\Car
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * Set carService
     *
     * @param \AppBundle\Entity\CarService $carService
     *
     * @return CarOwnerRequest
     */
    public function setCarService(\AppBundle\Entity\CarService $carService)
    {
        $this->carService = $carService;

        return $this;
    }

    /**
     * Get carService
     *
     * @return \AppBundle\Entity\CarService
     */
    public function getCarService()
    {
        return $this->carService;
    }

    /**
     * Set carOwner
     *
     * @param \AppBundle\Entity\CarOwner $carOwner
     *
     * @return CarOwnerRequest
     */
    public function setCarOwner(\AppBundle\Entity\CarOwner $carOwner)
    {
        $this->carOwner = $carOwner;

        return $this;
    }

    /**
     * Get carOwner
     *
     * @return \AppBundle\Entity\CarOwner
     */
    public function getCarOwner()
    {
        return $this->carOwner;
    }

    /**
     * Add service
     *
     * @param \AppBundle\Entity\Service $service
     *
     * @return CarOwnerRequest
     */
    public function addService(\AppBundle\Entity\Service $service)
    {
        $this->services[] = $service;

        return $this;
    }

    /**
     * Remove service
     *
     * @param \AppBundle\Entity\Service $service
     */
    public function removeService(\AppBundle\Entity\Service $service)
    {
        $this->services->removeElement($service);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    public function setServices(\Doctrine\Common\Collections\ArrayCollection $services)
    {
        return $this->services = $services;
    }

    /**
     * Add reason
     *
     * @param \AppBundle\Entity\ServiceReason $reason
     *
     * @return CarOwnerRequest
     */
    public function addReason(\AppBundle\Entity\ServiceReason $reason)
    {
        $this->reasons[] = $reason;

        return $this;
    }

    /**
     * Remove reason
     *
     * @param \AppBundle\Entity\ServiceReason $reason
     */
    public function removeReason(\AppBundle\Entity\ServiceReason $reason)
    {
        $this->reasons->removeElement($reason);
    }

    /**
     * Get reasons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReasons()
    {
        return $this->reasons;
    }

    /**
     * Set review
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return CarOwnerRequest
     */
    public function setReview(\AppBundle\Entity\Review $review = null)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return \AppBundle\Entity\Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Add item
     *
     * @param \AppBundle\Entity\CarOwnerRequestItem $item
     *
     * @return CarOwnerRequest
     */
    public function addItem(\AppBundle\Entity\CarOwnerRequestItem $item)
    {
        $this->items[] = $item;
        $item->setRequest($this);

        return $this;
    }

    /**
     * Remove item
     *
     * @param \AppBundle\Entity\CarOwnerRequestItem $item
     */
    public function removeItem(\AppBundle\Entity\CarOwnerRequestItem $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    public function getWorkItems()
    {
        if (null === $this->workItems) {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('type', CarOwnerRequestItem::TYPE_WORKS));

            $this->workItems = $this->items->matching($criteria);
        }

        return $this->workItems;
    }

    public function getPartItems()
    {
        if (null === $this->partItems) {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('type', CarOwnerRequestItem::TYPE_PARTS));

            $this->partItems = $this->items->matching($criteria);
        }

        return $this->partItems;
    }

    public function getWorkItemsSum()
    {
        $sum = 0;
        /* @var $item CarOwnerRequestItem */
        foreach ($this->getWorkItems() as $item) {
            $sum += $item->getSum();
        }

        return $sum;
    }

    public function getPartItemsSum()
    {
        $sum = 0;
        /* @var $item CarOwnerRequestItem */
        foreach ($this->getPartItems() as $item) {
            $sum += $item->getSum();
        }

        return $sum;
    }

    /**
     * Set checkInDateTime
     *
     * @param \DateTime $checkInDateTime
     *
     * @return CarOwnerRequest
     */
    public function setCheckInDateTime($checkInDateTime)
    {
        $this->checkInDateTime = $checkInDateTime;

        return $this;
    }

    /**
     * Get checkInDateTime
     *
     * @return \DateTime
     */
    public function getCheckInDateTime()
    {
        return $this->checkInDateTime;
    }

    /**
     * Set checkOutDateTime
     *
     * @param \DateTime $checkOutDateTime
     *
     * @return CarOwnerRequest
     */
    public function setCheckOutDateTime($checkOutDateTime)
    {
        $this->checkOutDateTime = $checkOutDateTime;

        return $this;
    }

    /**
     * Get checkOutDateTime
     *
     * @return \DateTime
     */
    public function getCheckOutDateTime()
    {
        return $this->checkOutDateTime;
    }

    /**
     * Set masterInspector
     *
     * @param string $masterInspector
     *
     * @return CarOwnerRequest
     */
    public function setMasterInspector($masterInspector)
    {
        $this->masterInspector = $masterInspector;

        return $this;
    }

    /**
     * Get masterInspector
     *
     * @return string
     */
    public function getMasterInspector()
    {
        return $this->masterInspector;
    }

    /**
     * Set entryTime
     *
     * @param \DateTime $entryTime
     *
     * @return CarOwnerRequest
     */
    public function setEntryTime($entryTime)
    {
        $this->entryTime = $entryTime;

        return $this;
    }

    /**
     * Get entryTime
     *
     * @return \DateTime
     */
    public function getEntryTime()
    {
        return $this->entryTime;
    }

    /**
     * Set exitTime
     *
     * @param \DateTime $exitTime
     *
     * @return CarOwnerRequest
     */
    public function setExitTime($exitTime)
    {
        $this->exitTime = $exitTime;

        return $this;
    }

    /**
     * Get exitTime
     *
     * @return \DateTime
     */
    public function getExitTime()
    {
        return $this->exitTime;
    }

    /**
     * Set post
     *
     * @param \AppBundle\Entity\CarServicePost $post
     *
     * @return CarOwnerRequest
     */
    public function setPost(\AppBundle\Entity\CarServicePost $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \AppBundle\Entity\CarServicePost
     */
    public function getPost()
    {
        return $this->post;
    }

    public function isCancelable()
    {
        return in_array($this->status, array(CarOwnerRequest::STATUS_NEW, CarOwnerRequest::STATUS_ASSIGN, CarOwnerRequest::STATUS_REASSIGN, CarOwnerRequest::STATUS_POSTPONED ));
    }


    /**
     * Set schedule
     *
     * @param \AppBundle\Entity\CarServiceSchedule $schedule
     *
     * @return CarOwnerRequest
     */
    public function setSchedule(\AppBundle\Entity\CarServiceSchedule $schedule = null)
    {
        $this->schedule = $schedule;
        $schedule->setCarOwnerRequest($this);

        if (null === $schedule->getCarService()) {
            $schedule->setCarService($this->getCarService());
        }

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \AppBundle\Entity\CarServiceSchedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
}
