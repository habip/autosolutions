<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 *
 * @ORM\Table(name="CAR_SERVICE_SCHEDULE")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CarServiceScheduleRepository")
 * @AppAssert\Service(name="app.validator.car_service_post_available")
 * @AppAssert\Service(name="app.validator.schedule_time")
 */
class CarServiceSchedule
{
    const TYPE_DELETED = 'deleted';
    const TYPE_MOVED = 'moved';
    const TYPE_REQUEST_SCHEDULED = 'request';
    const TYPE_CLOSED = 'closed';


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
     * @var CarService
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarService", inversedBy="posts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID", nullable=false)
     * })
     **/
    private $carService;

    /**
     *
     * @var CarServicePost
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarServicePost")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="POST_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull(groups={"post"})
     **/
    private $post;

    /**
     *
     * @var CarOwnerRequest
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CarOwnerRequest", inversedBy="schedule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="REQUEST_ID", referencedColumnName="ID", nullable=true)
     * })
     **/
    private $carOwnerRequest;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="START_TIME", type="datetime", nullable=false)
     */
    private $startTime;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="END_TIME", type="datetime", nullable=false)
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_TIMESTAMP", type="datetime")
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
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return CarServiceSchedule
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return CarServiceSchedule
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set createdTimestamp
     *
     * @param \DateTime $createdTimestamp
     *
     * @return CarServiceSchedule
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
     * Set carService
     *
     * @param \AppBundle\Entity\CarService $carService
     *
     * @return CarServiceSchedule
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
     * Set post
     *
     * @param \AppBundle\Entity\CarServicePost $post
     *
     * @return CarServiceSchedule
     */
    public function setPost(\AppBundle\Entity\CarServicePost $post)
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

    /**
     * Set carOwnerRequest
     *
     * @param \AppBundle\Entity\CarOwnerRequest $carOwnerRequest
     *
     * @return CarServiceSchedule
     */
    public function setCarOwnerRequest(\AppBundle\Entity\CarOwnerRequest $carOwnerRequest = null)
    {
        $this->carOwnerRequest = $carOwnerRequest;

        if (null == $carOwnerRequest->getId() && null == $carOwnerRequest->getCarService()) {
            $carOwnerRequest->setCarService($this->carService);
        }

        return $this;
    }

    /**
     * Get carOwnerRequest
     *
     * @return \AppBundle\Entity\CarOwnerRequest
     */
    public function getCarOwnerRequest()
    {
        return $this->carOwnerRequest;
    }

}
