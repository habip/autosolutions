<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * CarService
 *
 * @ORM\Table(name="CAR_SERVICES")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\CarServiceRepository")
 *
 */
class CarService
{
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
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    private $name;

    /**
     *
     * @var \AppBundle\Entity\Locality
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Locality")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LOCALITY_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull
     */
    private $locality;

    /**
     *
     * @var \AppBundle\Entity\District
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\District")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="DISTRICT_ID", referencedColumnName="ID")
     * })
     */
    private $district;

    /**
     *
     * @var \AppBundle\Entity\MetroStation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MetroStation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="METRO_STATION_ID", referencedColumnName="ID")
     * })
     */
    private $station;

    /**
     *
     * @var \AppBundle\Entity\Highway
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Highway")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="HIGHWAY_ID", referencedColumnName="ID")
     * })
     */
    private $highway;

    /**
     * @var string
     *
     * @ORM\Column(name="STREET_ADDRESS", type="text", nullable=false)
     */
    private $streetAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="PHONE", type="string", length=50, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="text", nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="DIRECTOR", type="string", length=255, nullable=false)
     */
    private $director;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="SPECIAL", type="text", nullable=true)
     */
    private $special;

    /**
     * @var string
     *
     * @ORM\Column(name="SITE", type="text", nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="CLIENTS_COUNT", type="text", nullable=true)
     */
    private $clientsCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_STOREHOUSE_AVAILABLE", type="boolean", nullable=true)
     */
    private $isStorehouseAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_DETAIL_ORDER_AVAILABLE", type="boolean", nullable=true)
     */
    private $isDetailOrderAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_INSPECTOR_AVAILABLE", type="boolean", nullable=true)
     */
    private $isInspectorAvailable;

    /**
     * @var string
     *
     * @ORM\Column(name="WORKING_HOURS", type="string", length=255, nullable=true)
     */
    private $workingHours;

    /**
     * @var string
     *
     * @ORM\Column(name="WORKING_DAYS_IN_WEEK", type="string", length=255, nullable=true)
     */
    private $workingDaysInWeek;

    /**
     * @var string
     *
     * @ORM\Column(name="LATITUDE", type="string", length=255, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="LONGITUDE", type="string", length=255, nullable=true)
     */
    private $longitude;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_OFFICIAL", type="boolean", nullable=false)
     */
    private $isOfficial;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Brand")
     * @ORM\JoinTable(name="CAR_SERVICE_BRANDS",
     *     joinColumns={@ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="BRAND_ID", referencedColumnName="ID")}
     * )
     **/
    private $servedCarBrands;


    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ServiceType", mappedBy="carService")
     */
    private $serviceTypes;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\PaymentType")
     * @ORM\JoinTable(name="CAR_SERVICES_PAYMENT_TYPES",
     *     joinColumns={@ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="PAYMENT_TYPE_ID", referencedColumnName="ID")}
     * )
     **/
    private $paymentTypes;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ServiceGroup")
     * @ORM\JoinTable(name="CAR_SERVICE_SERVICE_GROUPS",
     *     joinColumns={@ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="SERVICE_GROUP_ID", referencedColumnName="ID")}
     * )
     **/
    private $serviceGroups;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Service")
     * @ORM\JoinTable(name="CAR_SERVICE_SERVICES",
     *     joinColumns={@ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="SERVICE_ID", referencedColumnName="ID")}
     * )
     **/
    private $services;

    /**
     * @var AppBundle\Entity\Company
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="carServices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COMPANY_ID", referencedColumnName="ID")
     * })
     */
    private $company;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CarOwnerRequest", mappedBy="carService")
     */
    private $carOwnerRequests;


    /**
     * @var string
     *
     * @ORM\Column(name="WAITING_PLACES_COUNT", type="string", length=255, nullable=true)
     */
    private $waitingPlacesCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_COMFORTABLE_WAITING_PLACES_AVAILABLE", type="boolean", nullable=true)
     */
    private $isComfortableWaitingPlacesAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_FREE_WIFI_AVAILABLE", type="boolean", nullable=true)
     */
    private $isFreeWIFIAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_HOT_DRINK_AVAILABLE", type="boolean", nullable=true)
     */
    private $isHotDrinkAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_COLD_DRINK_AVAILABLE", type="boolean", nullable=true)
     */
    private $isColdDrinkAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_FOOD_AVAILABLE", type="boolean", nullable=true)
     */
    private $isFoodAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_ACCESS_TO_REPAIR_ZONE_AVAILABLE", type="boolean", nullable=true)
     */
    private $isAccessToRepairZoneAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_VISUAL_ACCESS_TO_REPAIR_ZONE_AVAILABLE", type="boolean", nullable=true)
     */
    private $isVisualAccessToRepairZoneAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_VIDEO_ACCESS_TO_REPAIR_ZONE_AVAILABLE", type="boolean", nullable=true)
     */
    private $isVideoAccessToRepairZoneAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_TV_AVAILABLE", type="boolean", nullable=true)
     */
    private $isTVAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_FREE_TRANSPORT_SERVICE_AVAILABLE", type="boolean", nullable=true)
     */
    private $isFreeTransportServiceAvailable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_REPLACEMENT_CAR_SERVICE_AVAILABLE", type="boolean", nullable=true)
     */
    private $isReplacementCarServiceAvailable;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDITIONAL", type="string", length=255, nullable=true)
     */
    private $additional;

    /**
     * @var string
     *
     * @ORM\Column(name="GUEST_PARKING_COUNT", type="string", length=255, nullable=true)
     */
    private $guestParkingCount;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LOGO_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $logo;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $image;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="INSPECTOR_ZONE_IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $inspectorZoneImage;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CLIENT_ZONE_IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $clientZoneImage;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="WASHING_ZONE_IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $washingZoneImage;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TIRE_SERVICE_ZONE_IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $tireServiceZoneImage;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BENCH_REPAIR_ZONE_IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $benchRepairZoneImage;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BODY_REPAIR_ZONE_IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $bodyRepairZoneImage;

    /**
     *
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="EMPLOYEES_IMAGE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $employeesImage;

    /**
     *
     * @var boolean
     * @ORM\Column(name="IS_24_HRS", type="boolean", nullable=false)
     */
    private $is24Hrs = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MON_START", type="time", nullable=true)
     */
    private $monStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MON_END", type="time", nullable=true)
     */
    private $monEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_MON_DAY_OFF", type="boolean", nullable=false)
     */
    private $isMonDayOff = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TUE_START", type="time", nullable=true)
     */
    private $tueStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TUE_END", type="time", nullable=true)
     */
    private $tueEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_TUE_DAY_OFF", type="boolean", nullable=false)
     */
    private $isTueDayOff = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="WED_START", type="time", nullable=true)
     */
    private $wedStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="WED_END", type="time", nullable=true)
     */
    private $wedEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_WED_DAY_OFF", type="boolean", nullable=false)
     */
    private $isWedDayOff = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="THU_START", type="time", nullable=true)
     */
    private $thuStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="THU_END", type="time", nullable=true)
     */
    private $thuEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_THU_DAY_OFF", type="boolean", nullable=false)
     */
    private $isThuDayOff = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FRI_START", type="time", nullable=true)
     */
    private $friStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FRI_END", type="time", nullable=true)
     */
    private $friEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_FRI_DAY_OFF", type="boolean", nullable=false)
     */
    private $isFriDayOff = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SAT_START", type="time", nullable=true)
     */
    private $satStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SAT_END", type="time", nullable=true)
     */
    private $satEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_SAT_DAY_OFF", type="boolean", nullable=false)
     */
    private $isSatDayOff = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SUN_START", type="time", nullable=true)
     */
    private $sunStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SUN_END", type="time", nullable=true)
     */
    private $sunEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_SUN_DAY_OFF", type="boolean", nullable=false)
     */
    private $isSunDayOff = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_BLOCKED", type="boolean", nullable=false)
     */
    private $isBlocked = false;

    /**
     *
     * @var float
     *
     * @ORM\Column(name="AVERAGE_RATING", type="float", nullable=true)
     */
    private $averageRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="SUM_RATING", type="bigint", nullable=true)
     */
    private $sumRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="RATING_5_COUNT", type="integer", nullable=false)
     */
    private $rating5Count = 0;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="RATING_4_COUNT", type="integer", nullable=false)
     */
    private $rating4Count = 0;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="RATING_3_COUNT", type="integer", nullable=false)
     */
    private $rating3Count = 0;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="RATING_2_COUNT", type="integer", nullable=false)
     */
    private $rating2Count = 0;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="RATING_1_COUNT", type="integer", nullable=false)
     */
    private $rating1Count = 0;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="REVIEW_COUNT", type="integer", nullable=false)
     */
    private $reviewCount = 0;

    /**
     *
     * @var float
     *
     * @ORM\Column(name="AVERAGE_DESCRIPTION_RATING", type="float", nullable=true)
     */
    private $averageDescriptionRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="SUM_DESCRIPTION_RATING", type="bigint", nullable=true)
     */
    private $sumDescriptionRating;

    /**
     *
     * @var float
     *
     * @ORM\Column(name="AVERAGE_COMMUNICATION_RATING", type="float", nullable=true)
     */
    private $averageCommunicationRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="SUM_COMMUNICATION_RATING", type="bigint", nullable=true)
     */
    private $sumCommunicationRating;

    /**
     *
     * @var float
     *
     * @ORM\Column(name="AVERAGE_PRICE_RATING", type="float", nullable=true)
     */
    private $averagePriceRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="SUM_PRICE_RATING", type="bigint", nullable=true)
     */
    private $sumPriceRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="DETAILED_REVIEW_COUNT", type="integer", nullable=false)
     */
    private $detailedReviewCount = 0;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CarServicePost", mappedBy="carService")
     */
    private $posts;

    /**
     *
     * Minimal time interval in minutes for one client service by one post
     *
     * @var integer
     */
    private $timeInterval = 30;

    /**
     * The number of days on which record is available
     *
     * @var integer
     */
    private $recordingDaysAhead = 5;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Employee", mappedBy="carService")
     */
    private $employees;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->servedCarBrands = new \Doctrine\Common\Collections\ArrayCollection();
        $this->serviceGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->services = new \Doctrine\Common\Collections\ArrayCollection();
        $this->carOwnerRequests = new \Doctrine\Common\Collections\ArrayCollection();
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->serviceTypes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return CarService
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
     * Set streetAddress
     *
     * @param string $streetAddress
     *
     * @return CarService
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return CarService
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
        $phone = preg_replace('/[^A-Za-z0-9\- ]/', '', $this->phone);
        $phoneArr = explode(' ',$phone);
        $phoneArr[0][0] = $phoneArr[0][0]=='7' ? 8 : $phoneArr[0][0];
        
        return $phoneArr[0];
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return CarService
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
     * Set director
     *
     * @param string $director
     *
     * @return CarService
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return CarService
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
     * Set special
     *
     * @param string $special
     *
     * @return CarService
     */
    public function setSpecial($special)
    {
        $this->special = $special;

        return $this;
    }

    /**
     * Get special
     *
     * @return string
     */
    public function getSpecial()
    {
        return $this->special;
    }

    /**
     * Set workingHours
     *
     * @param string $workingHours
     *
     * @return CarService
     */
    public function setWorkingHours($workingHours)
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    /**
     * Get workingHours
     *
     * @return string
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * Set locality
     *
     * @param \AppBundle\Entity\Locality $locality
     *
     * @return CarService
     */
    public function setLocality(\AppBundle\Entity\Locality $locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return \AppBundle\Entity\Locality
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set district
     *
     * @param \AppBundle\Entity\District $district
     *
     * @return CarService
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return \AppBundle\Entity\District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Add servedCarBrand
     *
     * @param \AppBundle\Entity\Brand $servedCarBrand
     *
     * @return CarService
     */
    public function addServedCarBrand(\AppBundle\Entity\Brand $servedCarBrand)
    {
        $this->servedCarBrands[] = $servedCarBrand;

        return $this;
    }

    /**
     * Remove servedCarBrand
     *
     * @param \AppBundle\Entity\Brand $servedCarBrand
     */
    public function removeServedCarBrand(\AppBundle\Entity\Brand $servedCarBrand)
    {
        $this->servedCarBrands->removeElement($servedCarBrand);
    }

    /**
     * Get servedCarBrands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServedCarBrands()
    {
        return $this->servedCarBrands;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return CarService
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
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

    /**
     * Add serviceGroup
     *
     * @param \AppBundle\Entity\ServiceGroup $serviceGroup
     *
     * @return CarService
     */
    public function addServiceGroup(\AppBundle\Entity\ServiceGroup $serviceGroup)
    {
        $this->serviceGroups[] = $serviceGroup;

        return $this;
    }

    /**
     * Remove serviceGroup
     *
     * @param \AppBundle\Entity\Service $serviceGroup
     */
    public function removeServiceGroup(\AppBundle\Entity\ServiceGroup $serviceGroup)
    {
        $this->serviceGroups->removeElement($serviceGroup);
    }

    /**
     * Get serviceGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceGroups()
    {
        $groups = [];
        foreach ($this->services as $service) {
            if (!isset($groups[$service->getGroup()->getId()])) {
                $groups[$service->getGroup()->getId()] = $service->getGroup();
            }
        }

        return new \Doctrine\Common\Collections\ArrayCollection(array_values($groups));
    }

    /**
     * Add service
     *
     * @param \AppBundle\Entity\Service $service
     *
     * @return CarService
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

    /**
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     *
     * @return CarService
     */
    public function setCompany(\AppBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \AppBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Add carOwnerRequest
     *
     * @param \AppBundle\Entity\CarOwnerRequest $carOwnerRequest
     *
     * @return CarService
     */
    public function addCarOwnerRequest(\AppBundle\Entity\CarOwnerRequest $carOwnerRequest)
    {
        $this->carOwnerRequests[] = $carOwnerRequest;

        return $this;
    }

    /**
     * Remove carOwnerRequest
     *
     * @param \AppBundle\Entity\CarOwnerRequest $carOwnerRequest
     */
    public function removeCarOwnerRequest(\AppBundle\Entity\CarOwnerRequest $carOwnerRequest)
    {
        $this->carOwnerRequests->removeElement($carOwnerRequest);
    }

    /**
     * Get carOwnerRequests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCarOwnerRequests()
    {
        return $this->carOwnerRequests;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return CarService
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return CarService
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set isOfficial
     *
     * @param boolean $isOfficial
     *
     * @return CarService
     */
    public function setIsOfficial($isOfficial)
    {
        $this->isOfficial = $isOfficial;

        return $this;
    }

    /**
     * Get isOfficial
     *
     * @return boolean
     */
    public function getIsOfficial()
    {
        return $this->isOfficial;
    }

    /**
     * Set station
     *
     * @param \AppBundle\Entity\MetroStation $station
     *
     * @return CarService
     */
    public function setStation(\AppBundle\Entity\MetroStation $station = null)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get station
     *
     * @return \AppBundle\Entity\MetroStation
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set highway
     *
     * @param \AppBundle\Entity\Highway $highway
     *
     * @return CarService
     */
    public function setHighway(\AppBundle\Entity\Highway $highway = null)
    {
        $this->highway = $highway;

        return $this;
    }

    /**
     * Get highway
     *
     * @return \AppBundle\Entity\Highway
     */
    public function getHighway()
    {
        return $this->highway;
    }

    /**
     * Set logo
     *
     * @param \AppBundle\Entity\Image $logo
     *
     * @return CarService
     */
    public function setLogo(\AppBundle\Entity\Image $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \AppBundle\Entity\Image
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return CarService
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }


    /**
     * Set isStorehouseAvailable
     *
     * @param boolean $isStorehouseAvailable
     *
     * @return CarService
     */
    public function setIsStorehouseAvailable($isStorehouseAvailable)
    {
        $this->isStorehouseAvailable = $isStorehouseAvailable;

        return $this;
    }

    /**
     * Get isStorehouseAvailable
     *
     * @return boolean
     */
    public function getIsStorehouseAvailable()
    {
        return $this->isStorehouseAvailable;
    }

    /**
     * Set isDetailOrderAvailable
     *
     * @param boolean $isDetailOrderAvailable
     *
     * @return CarService
     */
    public function setIsDetailOrderAvailable($isDetailOrderAvailable)
    {
        $this->isDetailOrderAvailable = $isDetailOrderAvailable;

        return $this;
    }

    /**
     * Get isDetailOrderAvailable
     *
     * @return boolean
     */
    public function getIsDetailOrderAvailable()
    {
        return $this->isDetailOrderAvailable;
    }

    /**
     * Set workingDaysInWeek
     *
     * @param string $workingDaysInWeek
     *
     * @return CarService
     */
    public function setWorkingDaysInWeek($workingDaysInWeek)
    {
        $this->workingDaysInWeek = $workingDaysInWeek;

        return $this;
    }

    /**
     * Get workingDaysInWeek
     *
     * @return string
     */
    public function getWorkingDaysInWeek()
    {
        return $this->workingDaysInWeek;
    }

    /**
     * Set waitingPlacesCount
     *
     * @param string $waitingPlacesCount
     *
     * @return CarService
     */
    public function setWaitingPlacesCount($waitingPlacesCount)
    {
        $this->waitingPlacesCount = $waitingPlacesCount;

        return $this;
    }

    /**
     * Get waitingPlacesCount
     *
     * @return string
     */
    public function getWaitingPlacesCount()
    {
        return $this->waitingPlacesCount;
    }

    /**
     * Set isComfortableWaitingPlacesAvailable
     *
     * @param boolean $isComfortableWaitingPlacesAvailable
     *
     * @return CarService
     */
    public function setIsComfortableWaitingPlacesAvailable($isComfortableWaitingPlacesAvailable)
    {
        $this->isComfortableWaitingPlacesAvailable = $isComfortableWaitingPlacesAvailable;

        return $this;
    }

    /**
     * Get isComfortableWaitingPlacesAvailable
     *
     * @return boolean
     */
    public function getIsComfortableWaitingPlacesAvailable()
    {
        return $this->isComfortableWaitingPlacesAvailable;
    }

    /**
     * Set isFreeWIFIAvailable
     *
     * @param boolean $isFreeWIFIAvailable
     *
     * @return CarService
     */
    public function setIsFreeWIFIAvailable($isFreeWIFIAvailable)
    {
        $this->isFreeWIFIAvailable = $isFreeWIFIAvailable;

        return $this;
    }

    /**
     * Get isFreeWIFIAvailable
     *
     * @return boolean
     */
    public function getIsFreeWIFIAvailable()
    {
        return $this->isFreeWIFIAvailable;
    }

    /**
     * Set isHotDrinkAvailable
     *
     * @param boolean $isHotDrinkAvailable
     *
     * @return CarService
     */
    public function setIsHotDrinkAvailable($isHotDrinkAvailable)
    {
        $this->isHotDrinkAvailable = $isHotDrinkAvailable;

        return $this;
    }

    /**
     * Get isHotDrinkAvailable
     *
     * @return boolean
     */
    public function getIsHotDrinkAvailable()
    {
        return $this->isHotDrinkAvailable;
    }

    /**
     * Set isFoodAvailable
     *
     * @param boolean $isFoodAvailable
     *
     * @return CarService
     */
    public function setIsFoodAvailable($isFoodAvailable)
    {
        $this->isFoodAvailable = $isFoodAvailable;

        return $this;
    }

    /**
     * Get isFoodAvailable
     *
     * @return boolean
     */
    public function getIsFoodAvailable()
    {
        return $this->isFoodAvailable;
    }

    /**
     * Set isAccessToRepairZoneAvailable
     *
     * @param boolean $isAccessToRepairZoneAvailable
     *
     * @return CarService
     */
    public function setIsAccessToRepairZoneAvailable($isAccessToRepairZoneAvailable)
    {
        $this->isAccessToRepairZoneAvailable = $isAccessToRepairZoneAvailable;

        return $this;
    }

    /**
     * Get isAccessToRepairZoneAvailable
     *
     * @return boolean
     */
    public function getIsAccessToRepairZoneAvailable()
    {
        return $this->isAccessToRepairZoneAvailable;
    }

    /**
     * Set isVisualAccessToRepairZoneAvailable
     *
     * @param boolean $isVisualAccessToRepairZoneAvailable
     *
     * @return CarService
     */
    public function setIsVisualAccessToRepairZoneAvailable($isVisualAccessToRepairZoneAvailable)
    {
        $this->isVisualAccessToRepairZoneAvailable = $isVisualAccessToRepairZoneAvailable;

        return $this;
    }

    /**
     * Get isVisualAccessToRepairZoneAvailable
     *
     * @return boolean
     */
    public function getIsVisualAccessToRepairZoneAvailable()
    {
        return $this->isVisualAccessToRepairZoneAvailable;
    }

    /**
     * Set isVideoAccessToRepairZoneAvailable
     *
     * @param boolean $isVideoAccessToRepairZoneAvailable
     *
     * @return CarService
     */
    public function setIsVideoAccessToRepairZoneAvailable($isVideoAccessToRepairZoneAvailable)
    {
        $this->isVideoAccessToRepairZoneAvailable = $isVideoAccessToRepairZoneAvailable;

        return $this;
    }

    /**
     * Get isVideoAccessToRepairZoneAvailable
     *
     * @return boolean
     */
    public function getIsVideoAccessToRepairZoneAvailable()
    {
        return $this->isVideoAccessToRepairZoneAvailable;
    }

    /**
     * Set isTVAvailable
     *
     * @param boolean $isTVAvailable
     *
     * @return CarService
     */
    public function setIsTVAvailable($isTVAvailable)
    {
        $this->isTVAvailable = $isTVAvailable;

        return $this;
    }

    /**
     * Get isTVAvailable
     *
     * @return boolean
     */
    public function getIsTVAvailable()
    {
        return $this->isTVAvailable;
    }

    /**
     * Set isFreeTransportServiceAvailable
     *
     * @param boolean $isFreeTransportServiceAvailable
     *
     * @return CarService
     */
    public function setIsFreeTransportServiceAvailable($isFreeTransportServiceAvailable)
    {
        $this->isFreeTransportServiceAvailable = $isFreeTransportServiceAvailable;

        return $this;
    }

    /**
     * Get isFreeTransportServiceAvailable
     *
     * @return boolean
     */
    public function getIsFreeTransportServiceAvailable()
    {
        return $this->isFreeTransportServiceAvailable;
    }

    /**
     * Set isReplacementCarServiceAvailable
     *
     * @param boolean $isReplacementCarServiceAvailable
     *
     * @return CarService
     */
    public function setIsReplacementCarServiceAvailable($isReplacementCarServiceAvailable)
    {
        $this->isReplacementCarServiceAvailable = $isReplacementCarServiceAvailable;

        return $this;
    }

    /**
     * Get isReplacementCarServiceAvailable
     *
     * @return boolean
     */
    public function getIsReplacementCarServiceAvailable()
    {
        return $this->isReplacementCarServiceAvailable;
    }

    /**
     * Set additional
     *
     * @param string $additional
     *
     * @return CarService
     */
    public function setAdditional($additional)
    {
        $this->additional = $additional;

        return $this;
    }

    /**
     * Get additional
     *
     * @return string
     */
    public function getAdditional()
    {
        return $this->additional;
    }

    /**
     * Set isGuestParkingAvailable
     *
     * @param boolean $isGuestParkingAvailable
     *
     * @return CarService
     */
    public function setIsGuestParkingAvailable($isGuestParkingAvailable)
    {
        $this->isGuestParkingAvailable = $isGuestParkingAvailable;

        return $this;
    }

    /**
     * Get isGuestParkingAvailable
     *
     * @return boolean
     */
    public function getIsGuestParkingAvailable()
    {
        return $this->isGuestParkingAvailable;
    }

    /**
     * Set guestParkingCount
     *
     * @param string $guestParkingCount
     *
     * @return CarService
     */
    public function setGuestParkingCount($guestParkingCount)
    {
        $this->guestParkingCount = $guestParkingCount;

        return $this;
    }

    /**
     * Get guestParkingCount
     *
     * @return string
     */
    public function getGuestParkingCount()
    {
        return $this->guestParkingCount;
    }

    /**
     * Set roadZoneImage
     *
     * @param \AppBundle\Entity\Image $roadZoneImage
     *
     * @return CarService
     */
    public function setRoadZoneImage(\AppBundle\Entity\Image $roadZoneImage = null)
    {
        $this->roadZoneImage = $roadZoneImage;

        return $this;
    }

    /**
     * Get roadZoneImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getRoadZoneImage()
    {
        return $this->roadZoneImage;
    }

    /**
     * Set inspectorZoneImage
     *
     * @param \AppBundle\Entity\Image $inspectorZoneImage
     *
     * @return CarService
     */
    public function setInspectorZoneImage(\AppBundle\Entity\Image $inspectorZoneImage = null)
    {
        $this->inspectorZoneImage = $inspectorZoneImage;

        return $this;
    }

    /**
     * Get inspectorZoneImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getInspectorZoneImage()
    {
        return $this->inspectorZoneImage;
    }

    /**
     * Set clientZoneImage
     *
     * @param \AppBundle\Entity\Image $clientZoneImage
     *
     * @return CarService
     */
    public function setClientZoneImage(\AppBundle\Entity\Image $clientZoneImage = null)
    {
        $this->clientZoneImage = $clientZoneImage;

        return $this;
    }

    /**
     * Get clientZoneImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getClientZoneImage()
    {
        return $this->clientZoneImage;
    }

    /**
     * Set washingZoneImage
     *
     * @param \AppBundle\Entity\Image $washingZoneImage
     *
     * @return CarService
     */
    public function setWashingZoneImage(\AppBundle\Entity\Image $washingZoneImage = null)
    {
        $this->washingZoneImage = $washingZoneImage;

        return $this;
    }

    /**
     * Get washingZoneImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getWashingZoneImage()
    {
        return $this->washingZoneImage;
    }

    /**
     * Set tireServiceZoneImage
     *
     * @param \AppBundle\Entity\Image $tireServiceZoneImage
     *
     * @return CarService
     */
    public function setTireServiceZoneImage(\AppBundle\Entity\Image $tireServiceZoneImage = null)
    {
        $this->tireServiceZoneImage = $tireServiceZoneImage;

        return $this;
    }

    /**
     * Get tireServiceZoneImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getTireServiceZoneImage()
    {
        return $this->tireServiceZoneImage;
    }

    /**
     * Set benchRepairZoneImage
     *
     * @param \AppBundle\Entity\Image $benchRepairZoneImage
     *
     * @return CarService
     */
    public function setBenchRepairZoneImage(\AppBundle\Entity\Image $benchRepairZoneImage = null)
    {
        $this->benchRepairZoneImage = $benchRepairZoneImage;

        return $this;
    }

    /**
     * Get benchRepairZoneImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getBenchRepairZoneImage()
    {
        return $this->benchRepairZoneImage;
    }

    /**
     * Set bodyRepairZoneImage
     *
     * @param \AppBundle\Entity\Image $bodyRepairZoneImage
     *
     * @return CarService
     */
    public function setBodyRepairZoneImage(\AppBundle\Entity\Image $bodyRepairZoneImage = null)
    {
        $this->bodyRepairZoneImage = $bodyRepairZoneImage;

        return $this;
    }

    /**
     * Get bodyRepairZoneImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getBodyRepairZoneImage()
    {
        return $this->bodyRepairZoneImage;
    }

    /**
     * Set employeesImage
     *
     * @param \AppBundle\Entity\Image $employeesImage
     *
     * @return CarService
     */
    public function setEmployeesImage(\AppBundle\Entity\Image $employeesImage = null)
    {
        $this->employeesImage = $employeesImage;

        return $this;
    }

    /**
     * Get employeesImage
     *
     * @return \AppBundle\Entity\Image
     */
    public function getEmployeesImage()
    {
        return $this->employeesImage;
    }

    /**
     * Set isInspectorAvailable
     *
     * @param boolean $isInspectorAvailable
     *
     * @return CarService
     */
    public function setIsInspectorAvailable($isInspectorAvailable)
    {
        $this->isInspectorAvailable = $isInspectorAvailable;

        return $this;
    }

    /**
     * Get isInspectorAvailable
     *
     * @return boolean
     */
    public function getIsInspectorAvailable()
    {
        return $this->isInspectorAvailable;
    }

    /**
     * Add serviceType
     *
     * @param \AppBundle\Entity\ServiceType $serviceType
     *
     * @return CarService
     */
    public function addServiceType(\AppBundle\Entity\ServiceType $serviceType)
    {
        $this->serviceTypes[] = $serviceType;
        $serviceType->setCarService($this);

        return $this;
    }

    /**
     * Remove serviceType
     *
     * @param \AppBundle\Entity\ServiceType $serviceType
     */
    public function removeServiceType(\AppBundle\Entity\ServiceType $serviceType)
    {
        $this->serviceTypes->removeElement($serviceType);
    }

    /**
     * Get serviceTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceTypes()
    {
        return $this->serviceTypes;
    }

    /**
     * Get paymentsTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentsTypes()
    {
        return $this->paymentsTypes;
    }

    /**
     * Set isColdDrinkAvailable
     *
     * @param boolean $isColdDrinkAvailable
     *
     * @return CarService
     */
    public function setIsColdDrinkAvailable($isColdDrinkAvailable)
    {
        $this->isColdDrinkAvailable = $isColdDrinkAvailable;

        return $this;
    }

    /**
     * Get isColdDrinkAvailable
     *
     * @return boolean
     */
    public function getIsColdDrinkAvailable()
    {
        return $this->isColdDrinkAvailable;
    }



    /**
     * Add paymentType
     *
     * @param \AppBundle\Entity\PaymentType $paymentType
     *
     * @return CarService
     */
    public function addPaymentType(\AppBundle\Entity\PaymentType $paymentType)
    {
        $this->paymentTypes[] = $paymentType;

        return $this;
    }

    /**
     * Remove paymentType
     *
     * @param \AppBundle\Entity\PaymentType $paymentType
     */
    public function removePaymentType(\AppBundle\Entity\PaymentType $paymentType)
    {
        $this->paymentTypes->removeElement($paymentType);
    }

    /**
     * Get paymentTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentTypes()
    {
        return $this->paymentTypes;
    }

    /**
     * Set clientsCount
     *
     * @param string $clientsCount
     *
     * @return CarService
     */
    public function setClientsCount($clientsCount)
    {
        $this->clientsCount = $clientsCount;

        return $this;
    }

    /**
     * Get clientsCount
     *
     * @return string
     */
    public function getClientsCount()
    {
        return $this->clientsCount;
    }

    /**
     * Set is24Hrs
     *
     * @param boolean $is24Hrs
     *
     * @return CarService
     */
    public function setIs24Hrs($is24Hrs)
    {
        $this->is24Hrs = $is24Hrs;

        return $this;
    }

    /**
     * Get is24Hrs
     *
     * @return boolean
     */
    public function getIs24Hrs()
    {
        return $this->is24Hrs;
    }

    /**
     * Set monStart
     *
     * @param \DateTime $monStart
     *
     * @return CarService
     */
    public function setMonStart($monStart)
    {
        $this->monStart = $monStart;

        return $this;
    }

    /**
     * Get monStart
     *
     * @return \DateTime
     */
    public function getMonStart()
    {
        return $this->monStart;
    }

    /**
     * Set monEnd
     *
     * @param \DateTime $monEnd
     *
     * @return CarService
     */
    public function setMonEnd($monEnd)
    {
        $this->monEnd = $monEnd;

        return $this;
    }

    /**
     * Get monEnd
     *
     * @return \DateTime
     */
    public function getMonEnd()
    {
        return $this->monEnd;
    }

    /**
     * Set isMonDayOff
     *
     * @param boolean $isMonDayOff
     *
     * @return CarService
     */
    public function setIsMonDayOff($isMonDayOff)
    {
        $this->isMonDayOff = $isMonDayOff;

        return $this;
    }

    /**
     * Get isMonDayOff
     *
     * @return boolean
     */
    public function getIsMonDayOff()
    {
        return $this->isMonDayOff;
    }

    /**
     * Set tueStart
     *
     * @param \DateTime $tueStart
     *
     * @return CarService
     */
    public function setTueStart($tueStart)
    {
        $this->tueStart = $tueStart;

        return $this;
    }

    /**
     * Get tueStart
     *
     * @return \DateTime
     */
    public function getTueStart()
    {
        return $this->tueStart;
    }

    /**
     * Set tueEnd
     *
     * @param \DateTime $tueEnd
     *
     * @return CarService
     */
    public function setTueEnd($tueEnd)
    {
        $this->tueEnd = $tueEnd;

        return $this;
    }

    /**
     * Get tueEnd
     *
     * @return \DateTime
     */
    public function getTueEnd()
    {
        return $this->tueEnd;
    }

    /**
     * Set isTueDayOff
     *
     * @param boolean $isTueDayOff
     *
     * @return CarService
     */
    public function setIsTueDayOff($isTueDayOff)
    {
        $this->isTueDayOff = $isTueDayOff;

        return $this;
    }

    /**
     * Get isTueDayOff
     *
     * @return boolean
     */
    public function getIsTueDayOff()
    {
        return $this->isTueDayOff;
    }

    /**
     * Set wedStart
     *
     * @param \DateTime $wedStart
     *
     * @return CarService
     */
    public function setWedStart($wedStart)
    {
        $this->wedStart = $wedStart;

        return $this;
    }

    /**
     * Get wedStart
     *
     * @return \DateTime
     */
    public function getWedStart()
    {
        return $this->wedStart;
    }

    /**
     * Set wedEnd
     *
     * @param \DateTime $wedEnd
     *
     * @return CarService
     */
    public function setWedEnd($wedEnd)
    {
        $this->wedEnd = $wedEnd;

        return $this;
    }

    /**
     * Get wedEnd
     *
     * @return \DateTime
     */
    public function getWedEnd()
    {
        return $this->wedEnd;
    }

    /**
     * Set isWedDayOff
     *
     * @param boolean $isWedDayOff
     *
     * @return CarService
     */
    public function setIsWedDayOff($isWedDayOff)
    {
        $this->isWedDayOff = $isWedDayOff;

        return $this;
    }

    /**
     * Get isWedDayOff
     *
     * @return boolean
     */
    public function getIsWedDayOff()
    {
        return $this->isWedDayOff;
    }

    /**
     * Set thuStart
     *
     * @param \DateTime $thuStart
     *
     * @return CarService
     */
    public function setThuStart($thuStart)
    {
        $this->thuStart = $thuStart;

        return $this;
    }

    /**
     * Get thuStart
     *
     * @return \DateTime
     */
    public function getThuStart()
    {
        return $this->thuStart;
    }

    /**
     * Set thuEnd
     *
     * @param \DateTime $thuEnd
     *
     * @return CarService
     */
    public function setThuEnd($thuEnd)
    {
        $this->thuEnd = $thuEnd;

        return $this;
    }

    /**
     * Get thuEnd
     *
     * @return \DateTime
     */
    public function getThuEnd()
    {
        return $this->thuEnd;
    }

    /**
     * Set isThuDayOff
     *
     * @param boolean $isThuDayOff
     *
     * @return CarService
     */
    public function setIsThuDayOff($isThuDayOff)
    {
        $this->isThuDayOff = $isThuDayOff;

        return $this;
    }

    /**
     * Get isThuDayOff
     *
     * @return boolean
     */
    public function getIsThuDayOff()
    {
        return $this->isThuDayOff;
    }

    /**
     * Set friStart
     *
     * @param \DateTime $friStart
     *
     * @return CarService
     */
    public function setFriStart($friStart)
    {
        $this->friStart = $friStart;

        return $this;
    }

    /**
     * Get friStart
     *
     * @return \DateTime
     */
    public function getFriStart()
    {
        return $this->friStart;
    }

    /**
     * Set friEnd
     *
     * @param \DateTime $friEnd
     *
     * @return CarService
     */
    public function setFriEnd($friEnd)
    {
        $this->friEnd = $friEnd;

        return $this;
    }

    /**
     * Get friEnd
     *
     * @return \DateTime
     */
    public function getFriEnd()
    {
        return $this->friEnd;
    }

    /**
     * Set isFriDayOff
     *
     * @param boolean $isFriDayOff
     *
     * @return CarService
     */
    public function setIsFriDayOff($isFriDayOff)
    {
        $this->isFriDayOff = $isFriDayOff;

        return $this;
    }

    /**
     * Get isFriDayOff
     *
     * @return boolean
     */
    public function getIsFriDayOff()
    {
        return $this->isFriDayOff;
    }

    /**
     * Set satStart
     *
     * @param \DateTime $satStart
     *
     * @return CarService
     */
    public function setSatStart($satStart)
    {
        $this->satStart = $satStart;

        return $this;
    }

    /**
     * Get satStart
     *
     * @return \DateTime
     */
    public function getSatStart()
    {
        return $this->satStart;
    }

    /**
     * Set satEnd
     *
     * @param \DateTime $satEnd
     *
     * @return CarService
     */
    public function setSatEnd($satEnd)
    {
        $this->satEnd = $satEnd;

        return $this;
    }

    /**
     * Get satEnd
     *
     * @return \DateTime
     */
    public function getSatEnd()
    {
        return $this->satEnd;
    }

    /**
     * Set isSatDayOff
     *
     * @param boolean $isSatDayOff
     *
     * @return CarService
     */
    public function setIsSatDayOff($isSatDayOff)
    {
        $this->isSatDayOff = $isSatDayOff;

        return $this;
    }

    /**
     * Get isSatDayOff
     *
     * @return boolean
     */
    public function getIsSatDayOff()
    {
        return $this->isSatDayOff;
    }

    /**
     * Set sunStart
     *
     * @param \DateTime $sunStart
     *
     * @return CarService
     */
    public function setSunStart($sunStart)
    {
        $this->sunStart = $sunStart;

        return $this;
    }

    /**
     * Get sunStart
     *
     * @return \DateTime
     */
    public function getSunStart()
    {
        return $this->sunStart;
    }

    /**
     * Set sunEnd
     *
     * @param \DateTime $sunEnd
     *
     * @return CarService
     */
    public function setSunEnd($sunEnd)
    {
        $this->sunEnd = $sunEnd;

        return $this;
    }

    /**
     * Get sunEnd
     *
     * @return \DateTime
     */
    public function getSunEnd()
    {
        return $this->sunEnd;
    }

    /**
     * Set isSunDayOff
     *
     * @param boolean $isSunDayOff
     *
     * @return CarService
     */
    public function setIsSunDayOff($isSunDayOff)
    {
        $this->isSunDayOff = $isSunDayOff;

        return $this;
    }

    /**
     * Get isSunDayOff
     *
     * @return boolean
     */
    public function getIsSunDayOff()
    {
        return $this->isSunDayOff;
    }

    /**
     * Set isBlocked
     *
     * @param boolean $isBlocked
     *
     * @return CarService
     */
    public function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    /**
     * Get isBlocked
     *
     * @return boolean
     */
    public function getIsBlocked()
    {
        return $this->isBlocked;
    }

    /**
     * Set averageRating
     *
     * @param float $averageRating
     *
     * @return CarService
     */
    public function setAverageRating($averageRating)
    {
        $this->averageRating = $averageRating;

        return $this;
    }

    /**
     * Get averageRating
     *
     * @return float
     */
    public function getAverageRating()
    {
        return $this->averageRating;
    }

    /**
     * Set rating5Count
     *
     * @param integer $rating5Count
     *
     * @return CarService
     */
    public function setRating5Count($rating5Count)
    {
        $this->rating5Count = $rating5Count;

        return $this;
    }

    /**
     * Get rating5Count
     *
     * @return integer
     */
    public function getRating5Count()
    {
        return $this->rating5Count;
    }

    /**
     * Set rating4Count
     *
     * @param integer $rating4Count
     *
     * @return CarService
     */
    public function setRating4Count($rating4Count)
    {
        $this->rating4Count = $rating4Count;

        return $this;
    }

    /**
     * Get rating4Count
     *
     * @return integer
     */
    public function getRating4Count()
    {
        return $this->rating4Count;
    }

    /**
     * Set rating3Count
     *
     * @param integer $rating3Count
     *
     * @return CarService
     */
    public function setRating3Count($rating3Count)
    {
        $this->rating3Count = $rating3Count;

        return $this;
    }

    /**
     * Get rating3Count
     *
     * @return integer
     */
    public function getRating3Count()
    {
        return $this->rating3Count;
    }

    /**
     * Set rating2Count
     *
     * @param integer $rating2Count
     *
     * @return CarService
     */
    public function setRating2Count($rating2Count)
    {
        $this->rating2Count = $rating2Count;

        return $this;
    }

    /**
     * Get rating2Count
     *
     * @return integer
     */
    public function getRating2Count()
    {
        return $this->rating2Count;
    }

    /**
     * Set rating1Count
     *
     * @param integer $rating1Count
     *
     * @return CarService
     */
    public function setRating1Count($rating1Count)
    {
        $this->rating1Count = $rating1Count;

        return $this;
    }

    /**
     * Get rating1Count
     *
     * @return integer
     */
    public function getRating1Count()
    {
        return $this->rating1Count;
    }

    /**
     * Set reviewCount
     *
     * @param integer $reviewCount
     *
     * @return CarService
     */
    public function setReviewCount($reviewCount)
    {
        $this->reviewCount = $reviewCount;

        return $this;
    }

    /**
     * Get reviewCount
     *
     * @return integer
     */
    public function getReviewCount()
    {
        return $this->reviewCount;
    }

    /**
     * Set sumRating
     *
     * @param integer $sumRating
     *
     * @return CarService
     */
    public function setSumRating($sumRating)
    {
        $this->sumRating = $sumRating;

        return $this;
    }

    /**
     * Get sumRating
     *
     * @return integer
     */
    public function getSumRating()
    {
        return $this->sumRating;
    }

    /**
     * Set averageDescriptionRating
     *
     * @param float $averageDescriptionRating
     *
     * @return CarService
     */
    public function setAverageDescriptionRating($averageDescriptionRating)
    {
        $this->averageDescriptionRating = $averageDescriptionRating;

        return $this;
    }

    /**
     * Get averageDescriptionRating
     *
     * @return float
     */
    public function getAverageDescriptionRating()
    {
        return $this->averageDescriptionRating;
    }

    /**
     * Set sumDescriptionRating
     *
     * @param integer $sumDescriptionRating
     *
     * @return CarService
     */
    public function setSumDescriptionRating($sumDescriptionRating)
    {
        $this->sumDescriptionRating = $sumDescriptionRating;

        return $this;
    }

    /**
     * Get sumDescriptionRating
     *
     * @return integer
     */
    public function getSumDescriptionRating()
    {
        return $this->sumDescriptionRating;
    }

    /**
     * Set averageCommunicationRating
     *
     * @param float $averageCommunicationRating
     *
     * @return CarService
     */
    public function setAverageCommunicationRating($averageCommunicationRating)
    {
        $this->averageCommunicationRating = $averageCommunicationRating;

        return $this;
    }

    /**
     * Get averageCommunicationRating
     *
     * @return float
     */
    public function getAverageCommunicationRating()
    {
        return $this->averageCommunicationRating;
    }

    /**
     * Set sumCommunicationRating
     *
     * @param integer $sumCommunicationRating
     *
     * @return CarService
     */
    public function setSumCommunicationRating($sumCommunicationRating)
    {
        $this->sumCommunicationRating = $sumCommunicationRating;

        return $this;
    }

    /**
     * Get sumCommunicationRating
     *
     * @return integer
     */
    public function getSumCommunicationRating()
    {
        return $this->sumCommunicationRating;
    }

    /**
     * Set averagePriceRating
     *
     * @param float $averagePriceRating
     *
     * @return CarService
     */
    public function setAveragePriceRating($averagePriceRating)
    {
        $this->averagePriceRating = $averagePriceRating;

        return $this;
    }

    /**
     * Get averagePriceRating
     *
     * @return float
     */
    public function getAveragePriceRating()
    {
        return $this->averagePriceRating;
    }

    /**
     * Set sumPriceRating
     *
     * @param integer $sumPriceRating
     *
     * @return CarService
     */
    public function setSumPriceRating($sumPriceRating)
    {
        $this->sumPriceRating = $sumPriceRating;

        return $this;
    }

    /**
     * Get sumPriceRating
     *
     * @return integer
     */
    public function getSumPriceRating()
    {
        return $this->sumPriceRating;
    }

    /**
     * Set detailedReviewCount
     *
     * @param integer $detailedReviewCount
     *
     * @return CarService
     */
    public function setDetailedReviewCount($detailedReviewCount)
    {
        $this->detailedReviewCount = $detailedReviewCount;

        return $this;
    }

    /**
     * Get detailedReviewCount
     *
     * @return integer
     */
    public function getDetailedReviewCount()
    {
        return $this->detailedReviewCount;
    }

    /**
     * Add post
     *
     * @param \AppBundle\Entity\CarServicePost $post
     *
     * @return CarService
     */
    public function addPost(\AppBundle\Entity\CarServicePost $post)
    {
        $this->posts[] = $post;
        $post->setCarService($this);

        return $this;
    }

    /**
     * Remove post
     *
     * @param \AppBundle\Entity\CarServicePost $post
     */
    public function removePost(\AppBundle\Entity\CarServicePost $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Get time interval
     *
     * @return integer
     */
    public function getTimeInterval()
    {
        return $this->timeInterval;
    }

    /**
     * Set days ahead
     *
     * @param integer $daysAhead
     * @return \AppBundle\Entity\CarService
     */
    public function setRecordingDaysAhead($daysAhead)
    {
        $this->recordingDaysAhead = $daysAhead;

        return $this;
    }

    /**
     * Get days ahead
     *
     * @return integer
     */
    public function getRecordingDaysAhead()
    {
        return $this->recordingDaysAhead;
    }

    /**
     * Set time interval
     *
     * @param integer $interval
     * @return \AppBundle\Entity\CarService
     */
    public function setTimeInterval($interval)
    {
        $this->timeInterval = $interval;

        return $this;
    }

    /**
     * Add employee
     *
     * @param \AppBundle\Entity\Employee $employee
     *
     * @return CarService
     */
    public function addEmployee(\AppBundle\Entity\Employee $employee)
    {
        $this->employees[] = $employee;

        return $this;
    }

    /**
     * Remove employee
     *
     * @param \AppBundle\Entity\Employee $employee
     */
    public function removeEmployee(\AppBundle\Entity\Employee $employee)
    {
        $this->employees->removeElement($employee);
    }

    /**
     * Get employees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    public function getWorkingHoursIterator($date)
    {
        if ($date instanceof \DateTime) {
            $d = $date;
        } else if (is_string($date)) {
            $d = new \DateTime($date);
        } else {
            throw new \Exception('Wrong arguments');
        }
        $weekDay = $d->format('D');

        $isDayOff = call_user_func(array($this, sprintf('getIs%sDayOff', $weekDay)));
        $start = call_user_func(array($this, sprintf('get%sStart', $weekDay)));
        $end = call_user_func(array($this, sprintf('get%sEnd', $weekDay)));

        if (!$isDayOff) {
            if ($this->is24Hrs) {
                $start = clone($d);
                $start->setTime(0, 0, 0);
                $end = clone($d);
                $end->setTime(23, 59, 59);
            } else {
                $start = clone($start);
                $start->setDate($d->format('Y'), $d->format('m'), $d->format('d'));
                $end = clone($end);
                $end->setDate($d->format('Y'), $d->format('m'), $d->format('d'));
            }

            $period = new \DateInterval(sprintf('PT%sM', $this->timeInterval));

            $gen = function(\DateTime $start, \DateTime $end, \DateInterval $period) {
                $c = clone($start);
                $e = $end;

                while ($c->getTimestamp() < $e->getTimestamp()) {
                    yield $c;
                    $c = clone($c);
                    $c->add($period);
                }
            };

            return $gen($start, $end, $period);
        } else {
            return array();
        }
    }

    private function isWeekDayTuned($weekDay)
    {
        $wd = strtolower($weekDay);
        $cwd = ucfirst($wd);

        $start      = $this->{$wd.'Start'};
        $end        = $this->{$wd.'End'};
        $isDayOff   = $this->{'is'.$cwd.'DayOff'};

        if (null !== $start && null !== $end || $isDayOff !== null || $this->is24Hrs === true) {
            return true;
        } else {
            return false;
        }
    }

    public function isSchedulable()
    {
        $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
        $isTuned = true;

        foreach ($days as $day) {
            if (!$this->isWeekDayTuned($day)) {
                $isTuned = false;
                break;
            }
        }

        return $isTuned && sizeof($this->getPosts()) > 0;
    }

    /**
     *
     * @param \AppBundle\Entity\CarService|array $carService
     * @param \DateTime $date
     * @return \DateTime
     * @throws \Exception
     */
    public static function sGetStartTime($carService, $date = null)
    {
        if ($date instanceof \DateTime) {
            $d = $date;
        } else if (is_string($date)) {
            $d = new \DateTime($date);
        } else if (null === $date) {
            $d = new \DateTime();
        } else {
            throw new \Exception('Wrong arguments');
        }
        $weekDay = $d->format('D');

        if ($carService instanceof CarService) {
            $isDayOff = call_user_func(array($carService, sprintf('getIs%sDayOff', $weekDay)));
            $start = call_user_func(array($carService, sprintf('get%sStart', $weekDay)));
        } else {
            $isDayOff = $carService[sprintf('is%sDayOff', $weekDay)];
            $start = $carService[sprintf('%sStart', strtolower($weekDay))];
        }

        $is24Hrs = $carService instanceof CarService ? $carService->getIs24Hrs() : $carService['is24Hrs'];
        if ($is24Hrs) {
            $start = clone $d;
            $start->setTime(0, 0, 0);

            return $start;
        } else {
            if (!$isDayOff && $start) {
                $start = clone($start);
                $start->setDate($d->format('Y'), $d->format('m'), $d->format('d'));

                return $start;
            } else {
                return null;
            }
        }
    }

    /**
     *
     * @param \DateTime|string $date
     * @return \DateTime
     * @throws \Exception
     */
    public function getStartTime($date = null)
    {
        return CarService::sGetStartTime($this, $date);
    }

    /**
     *
     * @param \AppBundle\Entity\CarService|array $carService
     * @param \DateTime|string $date
     * @return \DateTime
     * @throws \Exception
     */
    public static function sGetEndTime($carService, $date = null)
    {
        if ($date instanceof \DateTime) {
            $d = $date;
        } else if (is_string($date)) {
            $d = new \DateTime($date);
        } else if (null === $date) {
            $d = new \DateTime();
        } else {
            throw new \Exception('Wrong arguments');
        }
        $weekDay = $d->format('D');

        if ($carService instanceof CarService) {
            $isDayOff = call_user_func(array($carService, sprintf('getIs%sDayOff', $weekDay)));
            $end = call_user_func(array($carService, sprintf('get%sEnd', $weekDay)));
        } else {
            $isDayOff = $carService[sprintf('is%sDayOff', $weekDay)];
            $end = $carService[sprintf('%sEnd', strtolower($weekDay))];
        }

        $is24Hrs = $carService instanceof CarService ? $carService->getIs24Hrs() : $carService['is24Hrs'];
        if ($is24Hrs) {
            $end = clone $d;
            $end->setTime(23, 59, 59);

            return $end;
        } else {
            if (!$isDayOff && $end) {
                $end = clone($end);
                $end->setDate($d->format('Y'), $d->format('m'), $d->format('d'));

                return $end;
            } else {
                return null;
            }
        }
    }

    /**
     *
     * @param \DateTime|string $date
     * @return \DateTime
     * @throws \Exception
     */
    public function getEndTime($date = null)
    {
        return self::sGetEndTime($this, $date);
    }

    /**
     *
     * @param \AppBundle\Entity\CarService|array $carService
     * @param \DateTime|string $date
     * @return boolean
     * @throws \Exception
     */
    public static function sIsDayOff($carService, $date = null)
    {
        if ($date instanceof \DateTime) {
            $d = $date;
        } else if (is_string($date)) {
            $d = new \DateTime($date);
        } else if (null === $date) {
            $d = new \DateTime();
        } else {
            throw new \Exception('Wrong arguments');
        }
        $weekDay = $d->format('D');

        if ($carService instanceof CarService) {
            $isDayOff = call_user_func(array($carService, sprintf('getIs%sDayOff', $weekDay)));
        } else {
            $isDayOff = $carService[sprintf('is%sDayOff', $weekDay)];
        }

        return $isDayOff;
    }

    /**
     *
     * @param \DateTime|string $date
     * @return boolean
     * @throws \Exception
     */
    public function isDayOff($date = null)
    {
        return self::sIsDayOff($this, $date);
    }
}
