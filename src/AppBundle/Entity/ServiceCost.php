<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * ServiceCost
 *
 * @ORM\Table(name="SERVICE_COSTS")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceCostRepository")
 *
 */
class ServiceCost
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
     * @var \AppBundle\Entity\Service
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyService", inversedBy="serviceCosts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COMPANY_SERVICE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $companyService;

    /**
     *
     * @var \AppBundle\Entity\VehicleType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VehicleType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="VEHICLE_TYPE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $vehicleType;

    /**
     *
     * @var decimal
     *
     * @ORM\Column(name="COST", type="decimal", length=15, scale=2, nullable=false)
     * @Assert\NotNull
     */
    private $cost;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="DURATION", type="integer", nullable=false)
     * @Assert\NotNull
     */
    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_TIMESTAMP", type="datetime", columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $createdTimestamp;

    public function __construct()
    {
        $this->createdTimestamp = new \DateTime();
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
     * Set cost
     *
     * @param string $cost
     *
     * @return ServiceCost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return ServiceCost
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set companyService
     *
     * @param \AppBundle\Entity\CompanyService $companyService
     *
     * @return ServiceCost
     */
    public function setCompanyService(\AppBundle\Entity\CompanyService $companyService = null)
    {
        $this->companyService = $companyService;

        return $this;
    }

    /**
     * Get companyService
     *
     * @return \AppBundle\Entity\CompanyService
     */
    public function getCompanyService()
    {
        return $this->companyService;
    }

    /**
     * Set createdTimestamp
     *
     * @param \DateTime $createdTimestamp
     *
     * @return ServiceCost
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
     * Set vehicleType
     *
     * @param \AppBundle\Entity\VehicleType $vehicleType
     *
     * @return ServiceCost
     */
    public function setVehicleType(\AppBundle\Entity\VehicleType $vehicleType = null)
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
}
