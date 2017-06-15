<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * CarOwner
 *
 * @ORM\Table(name="CAR_OWNERS")
 * @ORM\Entity()
 */
class CarOwner
{
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

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
     * @ORM\Column(name="FIRST_NAME", type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="LAST_NAME", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     *
     * @var \AppBundle\Entity\Locality
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Locality")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LOCALITY_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $locality;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="PHONE", type="string", length=35, nullable=true)
     */
    private $phone;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="NICKNAME", type="string", length=255, nullable=false)
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="GENDER", type="string", length=6, nullable=true, columnDefinition="enum('male','female') DEFAULT NULL")
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="BIRTHDAY", type="date", nullable=true)
     */
    private $birthday;

    /**
     *
     * @var \AppBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\User", inversedBy="carOwner")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="USER_ID", referencedColumnName="ID")
     * })
     * @Assert\Valid()
     */
    private $user;


    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Car", mappedBy="carOwner")
     */
    private $cars;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CarOwnerRequest", mappedBy="carOwner")
     */
    private $carOwnerRequests;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cars = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return CarOwner
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        if (null == $this->nickname) {
            $this->nickname = $this->firstName . (null == $this->lastName ? '' : ' ' . $this->lastName );
        }

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return CarOwner
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        if (null == $this->nickname) {
            $this->nickname = (null == $this->firstName ? '' : $this->firstName . ' ') . $this->lastName;
        }

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return CarOwner
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
        return $this->phone ?
            $this->phone :
            ($this->getUser() && $this->getUser()->getPhone() ?
                sprintf('+%s%s', $this->getUser()->getPhone()->getCountryCode(), $this->getUser()->getPhone()->getNationalNumber()) :
                null);
    }

    /**
     * Set locality
     *
     * @param \AppBundle\Entity\Locality $locality
     *
     * @return CarOwner
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return CarOwner
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
     * Add car
     *
     * @param \AppBundle\Entity\Car $car
     *
     * @return CarOwner
     */
    public function addCar(\AppBundle\Entity\Car $car)
    {
        $this->cars[] = $car;
        $car->setCarOwner($this);

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
     * Add carOwnerRequest
     *
     * @param \AppBundle\Entity\CarOwnerRequest $carOwnerRequest
     *
     * @return CarOwner
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
     * Set nickname
     *
     * @param string $nickname
     *
     * @return CarOwner
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return CarOwner
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return CarOwner
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

}
