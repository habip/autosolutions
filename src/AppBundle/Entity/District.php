<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * District
 *
 * @ORM\Table(name="DISTRICTS")
 * @ORM\Entity()
 */
class District
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
     * @ORM\Column(name="NAME", type="string", length=150, nullable=false)
     */
    private $name;


    /**
     * @var \AppBundle\Entity\Locality
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Locality", inversedBy="districts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LOCALITY_ID", referencedColumnName="ID", nullable=false)
     * })
     */
    private $locality;



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
     * @return District
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
     * Set locality
     *
     * @param \AppBundle\Entity\Locality $locality
     *
     * @return District
     */
    public function setLocality(\AppBundle\Entity\Locality $locality = null)
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
