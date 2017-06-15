<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * Service
 *
 * @ORM\Table(name="SERVICE_GROUPS")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceGroupRepository")
 */
class ServiceGroup
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Service", mappedBy="group")
     */
    private $services;


    /**
     *
     * @var \AppBundle\Entity\ServiceReason
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceReason", inversedBy="groups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="REASON_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $reason;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->services = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add service
     *
     * @param \AppBundle\Entity\Service $service
     *
     * @return ServiceGroup
     */
    public function addService(\AppBundle\Entity\Service $service)
    {
        $this->services[] = $service;
        $service->setGroup($this);

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
     * Set reason
     *
     * @param \AppBundle\Entity\ServiceReason $reason
     *
     * @return ServiceGroup
     */
    public function setReason(\AppBundle\Entity\ServiceReason $reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return \AppBundle\Entity\ServiceReason
     */
    public function getReason()
    {
        return $this->reason;
    }


    /**
     * Set position
     *
     * @param integer $position
     *
     * @return ServiceGroup
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
}
