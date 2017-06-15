<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\AppBundle;

/**
 * CarModel
 *
 * @ORM\Table(name="CAR_MODELS")
 * @ORM\Entity()
 */
class CarModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var \AppBundle\Entity\VehicleType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VehicleType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="VEHICLE_TYPE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $vehicleType;

    /**
     * @var string
     *
     * @ORM\Column(name="VEHICLE_CLASS", type="string", length=2, nullable=true)
     */
    private $vehicleClass;

    /**
     * @var \AppBundle\Entity\Brand
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BRAND_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    private $brand;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Car", mappedBy="model")
     */
    private $cars;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cars = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CarModel
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
     * Set brand
     *
     * @param \AppBundle\Entity\Brand $brand
     *
     * @return CarModel
     */
    public function setBrand(\AppBundle\Entity\Brand $brand)
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
     * Add car
     *
     * @param \AppBundle\Entity\Car $car
     *
     * @return CarModel
     */
    public function addCar(\AppBundle\Entity\Car $car)
    {
        $this->cars[] = $car;

        return $this;
    }

    /**
     * Remove car
     *
     * @param \AppBundle\Entity\Car $car
     */
    public function removeCar(\AppBundle\Entity\Car $car)
    {
        $this->cars->removeElement($car);
    }

    /**
     * Get cars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCars()
    {
        return $this->cars;
    }

    /**
     * Set vehicleType
     *
     * @param \AppBundle\Entity\VehicleType $vehicleType
     *
     * @return CarModel
     */
    public function setVehicleType($vehicleType)
    {
        $this->vehicleType = $vehicleType;

        return $this;
    }

    /**
     * Get vehicleType
     *
     * @return \AppBundle\Entity\VehicleType
     */
    public function getVehicleType()
    {
        return $this->vehicleType;
    }

    /**
     * Set vehicleClass
     *
     * @param string $vehicleClass
     *
     * @return CarModel
     */
    public function setVehicleClass($vehicleClass)
    {
        $this->vehicleClass = $vehicleClass;

        return $this;
    }

    /**
     * Get vehicleClass
     *
     * @return string
     */
    public function getVehicleClass()
    {
        return $this->vehicleClass;
    }
}
