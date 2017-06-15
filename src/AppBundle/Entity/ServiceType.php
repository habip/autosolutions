<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="SERVICE_TYPES")
 * @ORM\Entity()
 */
class ServiceType
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
     * @var AppBundle\Entity\ServiceReason
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceReason")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SERVICE_REASON_ID", referencedColumnName="ID")
     * })
     */
    private $serviceReason;

    /**
     * @var string
     *
     * @ORM\Column(name="POST_COUNT", type="string", length=150, nullable=false)
     */
    private $postCount;

    /**
     * @var AppBundle\Entity\CarService
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarService", inversedBy="serviceTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID")
     * })
     */
    private $carService;


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
     * Set postCount
     *
     * @param string $postCount
     *
     * @return CarServiceType
     */
    public function setPostCount($postCount)
    {
        $this->postCount = $postCount;

        return $this;
    }

    /**
     * Get postCount
     *
     * @return string
     */
    public function getPostCount()
    {
        return $this->postCount;
    }

    /**
     * Set carService
     *
     * @param \AppBundle\Entity\CarService $carService
     *
     * @return ServiceType
     */
    public function setCarService(\AppBundle\Entity\CarService $carService = null)
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
     * Set serviceReason
     *
     * @param \AppBundle\Entity\ServiceReason $serviceReason
     *
     * @return ServiceType
     */
    public function setServiceReason(\AppBundle\Entity\ServiceReason $serviceReason = null)
    {
        $this->serviceReason = $serviceReason;

        return $this;
    }

    /**
     * Get serviceReason
     *
     * @return \AppBundle\Entity\ServiceReason
     */
    public function getServiceReason()
    {
        return $this->serviceReason;
    }
}
