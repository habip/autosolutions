<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="CAR_SERVICE_POST")
 * @ORM\Entity()
 *
 */
class CarServicePost
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
     *
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=100)
     */
    private $name;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=255)
     */
    private $description = '';

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
     * @return CarServicePost
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
     * Set description
     *
     * @param string $description
     *
     * @return CarServicePost
     */
    public function setDescription($description)
    {
        $this->description = (null === $description ? '' : $description);

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set carService
     *
     * @param \AppBundle\Entity\CarService $carService
     *
     * @return CarServicePost
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
}
