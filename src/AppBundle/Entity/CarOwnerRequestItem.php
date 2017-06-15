<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * CarOwnerRequestItem
 *
 * @ORM\Table(name="CAR_OWNER_REQUEST_ITEMS")
 * @ORM\Entity
 *
 */
class CarOwnerRequestItem
{
    const TYPE_WORKS = 'works';
    const TYPE_PARTS = 'parts';

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
     * @var string
     *
     * @ORM\Column(name="TYPE", type="string", length=15, nullable=false, columnDefinition="enum('works', 'parts') NOT NULL DEFAULT 'works'")
     */
    private $type = self::TYPE_WORKS;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="COST", type="decimal", length=15, scale=2, nullable=false)
     */
    private $cost;

    /**
     * @var float
     *
     * @ORM\Column(name="QUANTITY", type="float", nullable=false)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="SUM", type="decimal", length=15, scale=2, nullable=false)
     */
    private $sum;

    /**
     * @var CarOwnerRequest
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CarOwnerRequest", inversedBy="items")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="REQUEST_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    private $request;

    /**
     * @var CompanyService
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\CompanyService")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COMPANY_SERVICE_ID", referencedColumnName="ID", nullable=true)
     * })
     */
    private $companyService;

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
     * Set type
     *
     * @param string $type
     *
     * @return CarOwnerRequestItem
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
     * Set name
     *
     * @param string $name
     *
     * @return CarOwnerRequestItem
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
     * Set quantity
     *
     * @param float $quantity
     *
     * @return CarOwnerRequestItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set sum
     *
     * @param string $sum
     *
     * @return CarOwnerRequestItem
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum
     *
     * @return string
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Set request
     *
     * @param \AppBundle\Entity\CarOwnerRequest $request
     *
     * @return CarOwnerRequestItem
     */
    public function setRequest(\AppBundle\Entity\CarOwnerRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return \AppBundle\Entity\CarOwnerRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return CarOwnerRequestItem
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
     * Set companyService
     *
     * @param \AppBundle\Entity\CompanyService $companyService
     *
     * @return CarOwnerRequestItem
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
}
