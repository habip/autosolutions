<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\User;

/**
 * ChangeSubscriber
 *
 * @ORM\Table(name="CHANGE_SUBSCRIBERS")
 * @ORM\Entity()
 */
class ChangeSubscriber
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
     *
     * @var \AppBundle\Entity\Change
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Change", inversedBy="subscribers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CHANGE_ID", referencedColumnName="ID")
     * })
     */
    private $change;

    /**
     *
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="USER_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    private $user;

    /**
     * Set change
     *
     * @param \AppBundle\Entity\Change $change
     * @return ChangeSubscriber
     */
    public function setChange(\AppBundle\Entity\Change $change)
    {
        $this->change = $change;

        return $this;
    }

    /**
     * Get change
     *
     * @return \AppBundle\Entity\Change
     */
    public function getChange()
    {
        return $this->change;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return ChangeSubscriber
     */
    public function setUser(\AppBundle\Entity\User $user)
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
}
