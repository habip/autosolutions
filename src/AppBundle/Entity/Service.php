<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * Service
 *
 * @ORM\Table(name="SERVICES")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceRepository")
 */
class Service
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
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="POSITION", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DELETED_AT", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     *
     * @var \AppBundle\Entity\ServiceGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceGroup", inversedBy="services")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="GROUP_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $group;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyService", mappedBy="service")
     */
    private $companyServices;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->companyServices = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Service
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
     * Set group
     *
     * @param \AppBundle\Entity\ServiceGroup $group
     *
     * @return Service
     */
    public function setGroup(\AppBundle\Entity\ServiceGroup $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \AppBundle\Entity\ServiceGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add companyService
     *
     * @param \AppBundle\Entity\CompanyService $companyService
     *
     * @return Service
     */
    public function addCompanyService(\AppBundle\Entity\ServiceCost $companyService)
    {
        $this->companyServices[] = $companyService;
        $companyService->setService($this);

        return $this;
    }

    /**
     * Remove serviceCost
     *
     * @param \AppBundle\Entity\CompanyService $companyService
     */
    public function removeCompanyService(\AppBundle\Entity\CompanyService $companyService)
    {
        $this->companyServices->removeElement($companyService);
    }

    /**
     * Get companyServices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanyServices()
    {
        return $this->companyServices;
    }


    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Service
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Service
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
