<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * Brand
 *
 * @ORM\Table(name="DICTIONARIES")
 * @ORM\Entity
 *
 */
class Dictionary
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
     * @ORM\OneToMany(targetEntity="DictionaryItem", mappedBy="dictionary")
     */
    private $items;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
     * @return Brand
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
     * Add dictionary item
     *
     * @param \AppBundle\Entity\DictionaryItem $item
     *
     * @return Dictionary
     */
    public function addItem(\AppBundle\Entity\DictionaryItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove dictionary item
     *
     * @param \AppBundle\Entity\DictionaryItem $item
     */
    public function removeItem(\AppBundle\Entity\DictionaryItem $item)
    {
        $this->cars->removeElement($item);
    }

    /**
     * Get cars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
}
