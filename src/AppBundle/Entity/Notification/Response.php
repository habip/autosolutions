<?php

namespace AppBundle\Entity\Notification;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Response
 *
 * @ORM\Table(name="NOTIFICATION_RESPONSES")
 * @ORM\Entity
 *
 */
class Response
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
     * @var \Appbundle\Enitity\Notification\Notification
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Notification\Notification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="NOTIFICATION_ID", referencedColumnName="ID", nullable=false)
     * })
     * @Assert\NotNull()
     */
    private $notification;

    /**
     * @var string
     *
     * @ORM\Column(name="ACTION", type="string", length=255)
     */
    private $action;

    /**
     * @var array
     *
     * @ORM\Column(name="CHOICES", type="json_array")
     */
    private $choices;

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
     * Set action
     *
     * @param string $action
     *
     * @return Response
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set choices
     *
     * @param array $choices
     *
     * @return Response
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Get choices
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set notification
     *
     * @param \AppBundle\Entity\Notification\Notification $notification
     *
     * @return Response
     */
    public function setNotification(\AppBundle\Entity\Notification\Notification $notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return \AppBundle\Entity\Notification\Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
