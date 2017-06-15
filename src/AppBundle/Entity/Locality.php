<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Locality
 *
 * @ORM\Table(name="LOCALITIES",
 *      indexes={
 *          @ORM\Index(name="search_locality_idx", columns={"COUNTRY_ID","SIGNIFICANCE","NAME"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocalityRepository")
 */
class Locality
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=150, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="SIGNIFICANCE", type="integer")
     */
    private $significance;

    /**
     * @var \AppBundle\Entity\Country
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COUNTRY_ID", referencedColumnName="ID")
     * })
     */
    private $country;

    /**
     * @var \AppBundle\Entity\Region
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="REGION_ID", referencedColumnName="ID")
     * })
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="AREA_NAME", type="string", nullable=true)
     */
    private $areaName;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="District", mappedBy="locality")
     */
    private $districts;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MetroStation", mappedBy="locality")
     */
    private $stations;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Highway", mappedBy="locality")
     */
    private $highways;


    /**
     * @var string
     *
     * @ORM\Column(name="LATITUDE", type="decimal", precision=18, scale=12, nullable=true)
     */
    private $latitude;


    /**
     * @var string
     *
     * @ORM\Column(name="LONGITUDE", type="decimal", precision=18, scale=12, nullable=true)
     */
    private $longitude;

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
     * Set localityName
     *
     * @param string $localityName
     * @return Locality
     */
    public function setLocalityName($localityName)
    {
        $this->localityName = $localityName;

        return $this;
    }

    /**
     * Get localityName
     *
     * @return string
     */
    public function getLocalityName()
    {
        return $this->localityName;
    }

    /**
     * Set country
     *
     * @param \AppBundle\Entity\Country $country
     * @return Locality
     */
    public function setCountry(\AppBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \AppBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }
    /**
     * Set areaName
     *
     * @param string $areaName
     * @return Locality
     */
    public function setAreaName($areaName)
    {
        $this->areaName = $areaName;

        return $this;
    }

    /**
     * Get areaName
     *
     * @return string
     */
    public function getAreaName()
    {
        return $this->areaName;
    }

    /**
     * Set region
     *
     * @param \AppBundle\Entity\Region $region
     * @return Locality
     */
    public function setRegion(\AppBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \AppBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set significance
     *
     * @param integer $significance
     * @return Locality
     */
    public function setSignificance($significance)
    {
        $this->significance = $significance;

        return $this;
    }

    /**
     * Get significance
     *
     * @return integer
     */
    public function getSignificance()
    {
        return $this->significance;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Locality
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
     * Constructor
     */
    public function __construct()
    {
        $this->districts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->highways = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add district
     *
     * @param \AppBundle\Entity\District $district
     *
     * @return Locality
     */
    public function addDistrict(\AppBundle\Entity\District $district)
    {
        $this->districts[] = $district;

        return $this;
    }

    /**
     * Remove district
     *
     * @param \AppBundle\Entity\District $district
     */
    public function removeDistrict(\AppBundle\Entity\District $district)
    {
        $this->districts->removeElement($district);
    }

    /**
     * Get districts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDistricts()
    {
        return $this->districts;
    }

    /**
     * Add station
     *
     * @param \AppBundle\Entity\MetroStation $station
     *
     * @return Locality
     */
    public function addStation(\AppBundle\Entity\MetroStation $station)
    {
        $this->stations[] = $station;

        return $this;
    }

    /**
     * Remove station
     *
     * @param \AppBundle\Entity\MetroStation $station
     */
    public function removeStation(\AppBundle\Entity\MetroStation $station)
    {
        $this->stations->removeElement($station);
    }

    /**
     * Get stations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStations()
    {
        return $this->stations;
    }

    /**
     * Add highway
     *
     * @param \AppBundle\Entity\Highway $highway
     *
     * @return Locality
     */
    public function addHighway(\AppBundle\Entity\Highway $highway)
    {
        $this->highways[] = $highway;

        return $this;
    }

    /**
     * Remove highway
     *
     * @param \AppBundle\Entity\Highway $highway
     */
    public function removeHighway(\AppBundle\Entity\Highway $highway)
    {
        $this->highways->removeElement($highway);
    }

    /**
     * Get highways
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHighways()
    {
        return $this->highways;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Locality
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Locality
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
