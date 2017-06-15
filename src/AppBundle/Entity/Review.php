<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\DBAL\LockMode;


/**
 * Review
 *
 * @ORM\Table(name="REVIEWS")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\ReviewRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Review
{

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="ID", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="USER_ID", referencedColumnName="ID")
     * })
     */
    private $user;

    /**
     *
     * @var \AppBundle\Entity\CarService
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CarService")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CAR_SERVICE_ID", referencedColumnName="ID")
     * })
     */
    private $carService;

    /**
     *
     * @var \AppBundle\Entity\CarOwnerRequest
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\CarOwnerRequest", mappedBy="review")
     */
    private $carOwnerRequest;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="RATING", type="smallint", nullable=false)
     * @Assert\NotNull(message="Пожалуйста поставьте оценку по оказанной вам услуге. Нам очень важно узнать Ваше мнение!")
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      minMessage = "Рейтинг не может быть меньше 1",
     *      maxMessage = "Рейтинг не может быть больше 5"
     * )
     */
    private $rating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="DESCRIPTION_RATING", type="smallint", nullable=true)
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      minMessage = "Рейтинг не может быть меньше 1",
     *      maxMessage = "Рейтинг не может быть больше 5"
     * )
     */
    private $descriptionRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="COMMUNICATION_RATING", type="smallint", nullable=true)
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      minMessage = "Рейтинг не может быть меньше 1",
     *      maxMessage = "Рейтинг не может быть больше 5"
     * )
     */
    private $communicationRating;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="PRICE_RATING", type="smallint", nullable=true)
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      minMessage = "Рейтинг не может быть меньше 1",
     *      maxMessage = "Рейтинг не может быть больше 5"
     * )
     */
    private $priceRating;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="MESSAGE", type="text", nullable=false)
     */
    private $message = '';

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_TIMESTAMP", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $timestamp;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="RESPONSE", type="text", nullable=true)
     */
    private $response;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="RESPONSE_TIMESTAMP", type="datetime", nullable=true, columnDefinition="timestamp NULL")
     */
    private $responseTimestamp;

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
     * Set rating
     *
     * @param integer $rating
     *
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Review
     */
    public function setMessage($message)
    {
        $this->message = (null === $message ? '' : $message);

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Review
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Review
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set carService
     *
     * @param \AppBundle\Entity\CarService $carService
     *
     * @return Review
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
     * Set carOwnerRequest
     *
     * @param \AppBundle\Entity\CarOwnerRequest $carOwnerRequest
     *
     * @return Review
     */
    public function setCarOwnerRequest(\AppBundle\Entity\CarOwnerRequest $carOwnerRequest = null)
    {
        $this->carOwnerRequest = $carOwnerRequest;
        $this->carOwnerRequest->setReview($this);
        $this->carService = $carOwnerRequest->getCarService();

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

    /**
     * Set response
     *
     * @param string $response
     *
     * @return Review
     */
    public function setResponse($response)
    {
        if ($this->response === null && $response !== null) {
            $this->responseTimestamp = new \DateTime();
        }
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set responseTimestamp
     *
     * @param \DateTime $responseTimestamp
     *
     * @return Review
     */
    public function setResponseTimestamp($responseTimestamp)
    {
        $this->responseTimestamp = $responseTimestamp;

        return $this;
    }

    /**
     * Get responseTimestamp
     *
     * @return \DateTime
     */
    public function getResponseTimestamp()
    {
        return $this->responseTimestamp;
    }

    /**
     * Set descriptionRating
     *
     * @param integer $descriptionRating
     *
     * @return Review
     */
    public function setDescriptionRating($descriptionRating)
    {
        $this->descriptionRating = $descriptionRating;

        return $this;
    }

    /**
     * Get descriptionRating
     *
     * @return integer
     */
    public function getDescriptionRating()
    {
        return $this->descriptionRating;
    }

    /**
     * Set communicationRating
     *
     * @param integer $communicationRating
     *
     * @return Review
     */
    public function setCommunicationRating($communicationRating)
    {
        $this->communicationRating = $communicationRating;

        return $this;
    }

    /**
     * Get communicationRating
     *
     * @return integer
     */
    public function getCommunicationRating()
    {
        return $this->communicationRating;
    }

    /**
     * Set priceRating
     *
     * @param integer $priceRating
     *
     * @return Review
     */
    public function setPriceRating($priceRating)
    {
        $this->priceRating = $priceRating;

        return $this;
    }

    /**
     * Get priceRating
     *
     * @return integer
     */
    public function getPriceRating()
    {
        return $this->priceRating;
    }

    /**
     *
     * @param LifecycleEventArgs $event
     *
     * @ORM\PrePersist
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        /* @var $carService \AppBundle\Entity\CarService */
        $carService = $em->find('AppBundle:CarService', $this->carService->getId(), LockMode::PESSIMISTIC_WRITE);
        switch ($this->rating) {
            case 5:
                $carService->setRating5Count($carService->getRating5Count() + 1);
                break;
            case 4:
                $carService->setRating4Count($carService->getRating4Count() + 1);
                break;
            case 3:
                $carService->setRating3Count($carService->getRating3Count() + 1);
                break;
            case 2:
                $carService->setRating2Count($carService->getRating2Count() + 1);
                break;
            case 1:
                $carService->setRating1Count($carService->getRating1Count() + 1);
                break;
        }
        $carService->setReviewCount($carService->getReviewCount() + 1);
        $carService->setSumRating($carService->getSumRating() + $this->rating);
        $carService->setAverageRating($carService->getSumRating() / $carService->getReviewCount());

        if ($this->descriptionRating && $this->communicationRating && $this->priceRating) {
            $carService->setDetailedReviewCount($carService->getDetailedReviewCount() + 1);

            $carService->setSumDescriptionRating($carService->getSumDescriptionRating() + $this->descriptionRating);
            $carService->setAverageDescriptionRating($carService->getSumDescriptionRating() / $carService->getDetailedReviewCount());

            $carService->setSumCommunicationRating($carService->getSumCommunicationRating() + $this->communicationRating);
            $carService->setAverageCommunicationRating($carService->getSumCommunicationRating() / $carService->getDetailedReviewCount());

            $carService->setSumPriceRating($carService->getSumPriceRating() + $this->priceRating);
            $carService->setAveragePriceRating($carService->getSumPriceRating() / $carService->getDetailedReviewCount());
        }
    }
}
