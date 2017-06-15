<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints as RollerworksPassword;

/**
 * User
 *
 * @ORM\Table(name="USERS", uniqueConstraints={@ORM\UniqueConstraint(name="user_email_idx", columns={"EMAIL"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("email", groups={"start_registration"})
 * @UniqueEntity("phone", groups={"start_registration"})
 * @UniqueEntity("username", groups={"unique_username"})
 */
class User implements UserInterface, \Serializable
{
    const TYPE_COMPANY = 'company';
    const TYPE_CAR_OWNER = 'car_owner';
    const TYPE_AGENT = 'agent';
    const TYPE_EMPLOYEE = 'employee';

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
     * @var string
     *
     * @ORM\Column(name="TYPE", type="string", length=9, nullable=false, columnDefinition="enum('company', 'car_owner', 'agent', 'employee') NOT NULL DEFAULT 'car_owner'")
     */
    private $type = self::TYPE_CAR_OWNER;

    /**
     * @var string
     *
     * @ORM\Column(name="USERNAME", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="PASSWORD", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     * @RollerworksPassword\PasswordRequirements(minLength=6, requireNumbers=true, groups={"password"})
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="SALT", type="string", length=255, nullable=false)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=255, nullable=true)
     *
     * @Assert\NotNull(groups={"company"})
     */
    private $email;

    /**
     *
     * @var \libphonenumber\PhoneNumber
     *
     * @ORM\Column(name="PHONE", type="phone_number", nullable=true)
     * @AssertPhoneNumber(type="mobile", groups={"start_registration", "Default"})
     */
    private $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="REGISTRATION_DATE", type="datetime", nullable=false)
     */
    private $registrationDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_ACTIVE", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIRMATION_TOKEN", type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var booelan
     *
     * @ORM\Column(name="BLOCKED", type="boolean", nullable=true)
     */
    private $blocked;

    /**
     *
     * @var boolean
     *
     *  @ORM\Column(name="IS_ONLINE", type="boolean", nullable=false)
     */
    private $isOnline = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="LAST_VISIT", type="datetime", nullable=true, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $lastVisit;

    /**
     *
     * @var \AppBundle\Entity\Company
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\Company", mappedBy="user")
     */
    private $company;

    /**
     *
     * @var \AppBundle\Entity\CarOwner
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\CarOwner", mappedBy="user")
     */
    private $carOwner;

    /**
     *
     * @var \AppBundle\Entity\Agent
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\Agent", mappedBy="user")
     */
    private $agent;

    /**
     *
     * @var \AppBundle\Entity\Employee
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\Employee", mappedBy="user")
     */
    private $employee;

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
     * @var string
     *
     * @ORM\Column(name="QR_LOGIN", type="text", nullable=true)
     */
    private $qrLogin;


    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="QR_LOGIN_TIMESTAMP", type="datetime", nullable=true)
     */
    private $qrLoginTimestamp;

    /**
     * Code sended by sms message
     *
     * @var string
     */
    private $generatedCode;

    /**
     * Code generated time
     *
     * @var \DateTime
     */
    private $codeGeneratedTime;

    /**
     * Current users balance - is summary of all users Balance records
     *
     * @var string
     *
     * @ORM\Column(name="BALANCE", type="decimal", length=15, scale=2, nullable=false)
     */
    private $balance = '0.0';

    /**
     * Code from sms message
     *
     * @var string
     *
     * @Assert\Expression(
     *     "this.isCodeValid()",
     *     groups={"check_phone"},
     *     message="Не верный код, либо его срок действия истек (код действителен в течении 5 минут)"
     * )
     */
    private $code;

    private $phoneCheckPassed = false;

    public function __construct()
    {
        $this->isActive = false;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->registrationDate = new \DateTime();
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set plainPassword
     *
     * @param string $password
     * @return User
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
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
     * Set phone
     *
     * @param \libphonenumber\PhoneNumber $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return \libphonenumber\PhoneNumber
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set blocked
     *
     * @param boolean $blocked
     * @return User
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * Get blocked
     *
     * @return boolean
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * Set isOnline
     *
     * @param boolean $isOnline
     * @return User
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    /**
     * Get isOnline
     *
     * @return boolean
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    /**
     * Set lastVisit
     *
     * @param \DateTime $lastVisit
     * @return User
     */
    public function setLastVisit($lastVisit)
    {
        $this->lastVisit = $lastVisit;

        return $this;
    }

    /**
     * Get lastVisit
     *
     * @return \DateTime
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    public function getRoles() {
        return array('ROLE_USER', 'ROLE_' . strtoupper($this->type));
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getUsername() {
        return $this->username;
    }

    public function eraseCredentials() {
        $this->plainPassword = null;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
                $this->id,
                $this->type,
                $this->username,
                $this->password,
                $this->salt,
                $this->email,
                $this->isActive,
                $this->registrationDate,
                $this->phone,
                $this->generatedCode,
                $this->codeGeneratedTime,
                $this->code,
                $this->phoneCheckPassed,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
                $this->id,
                $this->type,
                $this->username,
                $this->password,
                $this->salt,
                $this->email,
                $this->isActive,
                $this->registrationDate,
                $this->phone,
                $this->generatedCode,
                $this->codeGeneratedTime,
                $this->code,
                $this->phoneCheckPassed,
        ) = $data;

    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * Set company
     *
     * @param \AppBundle\Entity\Company $company
     *
     * @return User
     */
    public function setCompany(\AppBundle\Entity\Company $company = null)
    {
        if (null === $this->company && null === $this->carOwner && null == $this->agent && null == $this->employee) {
            $this->company = $company;
            $this->type = self::TYPE_COMPANY;
            $company->setUser($this);
        } else {
            throw new \Exception('Setting company for user that already is car owner or company or agent');
        }

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
     * Set carOwner
     *
     * @param \AppBundle\Entity\CarOwner $carOwner
     *
     * @return User
     */
    public function setCarOwner(\AppBundle\Entity\CarOwner $carOwner = null)
    {
        if (null === $this->company && null === $this->carOwner && null == $this->agent && null == $this->employee) {
            $this->carOwner = $carOwner;
            $this->type = self::TYPE_CAR_OWNER;
            $carOwner->setUser($this);
        } else {
            throw new \Exception('Setting car owner for user that already is car owner or company or agent');
        }

        return $this;
    }

    /**
     * Get agent
     *
     * @return \AppBundle\Entity\Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set agent
     *
     * @param \AppBundle\Entity\Agent $agent
     *
     * @return User
     */
    public function setAgent(\AppBundle\Entity\Agent $agent = null)
    {
        if (null === $this->company && null === $this->carOwner && null == $this->agent && null == $this->employee) {
            $this->carOwner = $agent;
            $this->type = self::TYPE_AGENT;
            $agent->setUser($this);
        } else {
            throw new \Exception('Setting car owner for user that already is car owner or company');
        }

        return $this;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     * @return User
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
     * Set qrLogin
     *
     * @param string $qrLogin
     * @return User
     */
    public function setQrLogin($qrLogin)
    {
        $this->qrLogin = $qrLogin;

        return $this;
    }

    /**
     * Get qrLogin
     *
     * @return string
     */
    public function getQrLogin()
    {
        return $this->qrLogin;
    }

    /**
     * Set qrLoginTimestamp
     *
     * @param \DateTime $qrLoginTimestamp
     * @return User
     */
    public function setQrLoginTimestamp($qrLoginTimestamp)
    {
        $this->qrLoginTimestamp = $qrLoginTimestamp;

        return $this;
    }

    /**
     * Get qrLoginTimestamp
     *
     * @return \DateTime
     */
    public function getQrLoginTimestamp()
    {
        return $this->qrLoginTimestamp;
    }

    public function setGeneratedCode($code)
    {
        $this->generatedCode = $code;
        $this->codeGeneratedTime = new \DateTime();
        $this->phoneCheckPassed = false;
    }

    public function getGeneratedCode()
    {
        return $this->generatedCode;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function isCodeValid()
    {
        $latestCreateTime = new \DateTime('-5 minutes');
        $this->phoneCheckPassed = $this->phoneCheckPassed || ($this->code == $this->generatedCode && $this->codeGeneratedTime > $latestCreateTime);
        return $this->phoneCheckPassed;
    }


    /**
     * Set balace
     *
     * @param string $balace
     *
     * @return User
     */
    public function setBalance($balace)
    {
        $this->balance = $balace;

        return $this;
    }

    /**
     * Get balace
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set employee
     *
     * @param \AppBundle\Entity\Employee $employee
     *
     * @return User
     */
    public function setEmployee(\AppBundle\Entity\Employee $employee = null)
    {
        if (null === $this->company && null === $this->carOwner && null == $this->agent && null == $this->employee) {
            $this->employee = $employee;
            $this->type = self::TYPE_EMPLOYEE;
            if (null == $employee->getUser()) {
                $employee->setUser($this);
            }
        } else {
            throw new \Exception('Setting employee for user that already is car owner or company or agent or employee');
        }

        return $this;
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }
}
