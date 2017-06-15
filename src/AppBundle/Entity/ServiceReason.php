<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * ServiceReason
 *
 * @ORM\Table(name="SERVICE_REASONS")
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\ServiceReasonRepository")
 */
class ServiceReason
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
     * @ORM\Column(name="NAME", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ServiceGroup", mappedBy="reason")
     */
    private $groups;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ServiceReason
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
     * Add service group
     *
     * @param \AppBundle\Entity\ServiceGroup $group
     *
     * @return ServiceReason
     */
    public function addGroup(\AppBundle\Entity\ServiceGroup $group)
    {
        $this->groups[] = $group;
        $group->setReason($this);

        return $this;
    }

    /**
     * Remove service group
     *
     * @param \AppBundle\Entity\ServiceGroup $group
     */
    public function removeGroup(\AppBundle\Entity\ServiceGroup $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

}