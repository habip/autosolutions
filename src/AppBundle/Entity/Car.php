<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 *
 * Car
 *
 * @ORM\Table(name="CARS")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CarRepository")
 * @UniqueEntity(fields={"brand", "model", "year", "number", "carOwner"}, message="Такая машина уже существует")
 *
 */
class Car
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
     *
     * @var \AppBundle\Entity\Brand
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Brand", inversedBy="cars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BRAND_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull(message="Выберите марку автомобиля")
     */
    private $brand;

    /**
     * @var AppBundle\Entity\CarModel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarModel", inversedBy="cars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="MODEL_ID", referencedColumnName="ID")
     * })
     * @Assert\NotNull(message="Выберите модель автомобиля")
     */
    private $model;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="YEAR", type="integer", nullable=true)
     */
    private $year;


    /**
     *
     * @var integer
     *
     * @ORM\Column(name="MILEAGE", type="integer", nullable=true)
     */
    private $mileage;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMBER", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $number;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinTable(name="CAR_IMAGES",
     *     joinColumns={@ORM\JoinColumn(name="CAR_ID", referencedColumnName="ID")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="IMAGE_ID", referencedColumnName="ID")}
     * )
     **/
    private $images;

    /**
     * @var AppBundle\Entity\CarOwner
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarOwner", inversedBy="cars")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_OWNER_ID", referencedColumnName="ID")
     * })
     */
    private $carOwner;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CarOwnerRequest", mappedBy="car")
     */
    private $carOwnerRequests;

    /**
     * @var string
     *
     * @ORM\Column(name="IS_DELETED", type="boolean", nullable=false)
     */
    private $isDeleted = false;

    /**
     * @var string
     *
     * @ORM\Column(name="VIN", type="string", length=20, nullable=true)
     */
    private $vin;

    /**
     * @var string
     *
     * @ORM\Column(name="ENGINE_VOLUME", type="decimal", length=15, scale=3, nullable=true)
     */
    private $engineVolume;

    /**
     * @var string
     *
     * @ORM\Column(name="ENGINE_TYPE", type="string", length=20, nullable=true)
     */
    private $engineType;

    /**
     * @var string
     *
     * @ORM\Column(name="BODY_TYPE", type="string", length=20, nullable=true)
     */
    private $bodyType;

    /**
     * @var string
     *
     * @ORM\Column(name="DRIVE_TYPE", type="string", length=20, nullable=true)
     */
    private $driveType;

    /**
     * @var string
     *
     * @ORM\Column(name="CAR_TRAIDER", type="string", length=100, nullable=true)
     */
    private $carTraider;

    /**
     * @var string
     *
     * @ORM\Column(name="SALE_DATE", type="datetime", nullable=true)
     */
    private $saleDate;

    /**
     * @var string
     *
     * @ORM\Column(name="COLOR", type="string", length=20, nullable=true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="OWNER", type="string", length=255, nullable=true)
     */
    private $owner;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->carOwnerRequests = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set year
     *
     * @param integer $year
     *
     * @return Car
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set mileage
     *
     * @param integer $mileage
     *
     * @return Car
     */
    public function setMileage($mileage)
    {
        $this->mileage = $mileage;

        return $this;
    }

    /**
     * Get mileage
     *
     * @return integer
     */
    public function getMileage()
    {
        return $this->mileage;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Car
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set brand
     *
     * @param \AppBundle\Entity\Brand $brand
     *
     * @return Car
     */
    public function setBrand(\AppBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \AppBundle\Entity\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Car
     */
    public function addImage(\AppBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\Image $image
     */
    public function removeImage(\AppBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get image
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set carOwner
     *
     * @param \AppBundle\Entity\CarOwner $carOwner
     *
     * @return Car
     */
    public function setCarOwner(\AppBundle\Entity\CarOwner $carOwner = null)
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
     * Add carOwnerRequest
     *
     * @param \AppBundle\Entity\CarOwnerRequest $carOwnerRequest
     *
     * @return Car
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
     * Set model
     *
     * @param \AppBundle\Entity\CarModel $model
     *
     * @return Car
     */
    public function setModel(\AppBundle\Entity\CarModel $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return \AppBundle\Entity\CarModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return Car
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set vin
     *
     * @param string $vin
     *
     * @return Car
     */
    public function setVin($vin)
    {
        $this->vin = $vin;

        return $this;
    }

    /**
     * Get vin
     *
     * @return string
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * Set engineVolume
     *
     * @param string $engineVolume
     *
     * @return Car
     */
    public function setEngineVolume($engineVolume)
    {
        $this->engineVolume = $engineVolume;

        return $this;
    }

    /**
     * Get engineVolume
     *
     * @return string
     */
    public function getEngineVolume()
    {
        return $this->engineVolume;
    }

    /**
     * Set engineType
     *
     * @param string $engineType
     *
     * @return Car
     */
    public function setEngineType($engineType)
    {
        $this->engineType = $engineType;

        return $this;
    }

    /**
     * Get engineType
     *
     * @return string
     */
    public function getEngineType()
    {
        return $this->engineType;
    }

    /**
     * Set bodyType
     *
     * @param string $bodyType
     *
     * @return Car
     */
    public function setBodyType($bodyType)
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * Get bodyType
     *
     * @return string
     */
    public function getBodyType()
    {
        return $this->bodyType;
    }

    /**
     * Set driveType
     *
     * @param string $driveType
     *
     * @return Car
     */
    public function setDriveType($driveType)
    {
        $this->driveType = $driveType;

        return $this;
    }

    /**
     * Get driveType
     *
     * @return string
     */
    public function getDriveType()
    {
        return $this->driveType;
    }

    /**
     * Set carTraider
     *
     * @param string $carTraider
     *
     * @return Car
     */
    public function setCarTraider($carTraider)
    {
        $this->carTraider = $carTraider;

        return $this;
    }

    /**
     * Get carTraider
     *
     * @return string
     */
    public function getCarTraider()
    {
        return $this->carTraider;
    }

    /**
     * Set saleDate
     *
     * @param \DateTime $saleDate
     *
     * @return Car
     */
    public function setSaleDate($saleDate)
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    /**
     * Get saleDate
     *
     * @return \DateTime
     */
    public function getSaleDate()
    {
        return $this->saleDate;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Car
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set owner
     *
     * @param string $owner
     *
     * @return Car
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->brand->getName().' '.$this->model->getName().': '.$this->number;
    }
}
