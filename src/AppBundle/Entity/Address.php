<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Table(name="ADDRESSES")
 * @ORM\Entity
 */
class Address
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
     * @var \AppBundle\Entity\Locality
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Locality")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LOCALITY_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    private $locality;

    /**
     * @var string
     *
     * @ORM\Column(name="STREET_ADDRESS", type="text", nullable=false)
     */
    private $streetAddress;

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
     * Set streetAddress
     *
     * @param string $streetAddress
     * @return Address
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set locality
     *
     * @param \AppBundle\Entity\Locality $locality
     * @return Address
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
}
