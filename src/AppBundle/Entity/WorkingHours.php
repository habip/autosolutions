<?php
namespace AppBundle\Entity;

class WorkingHours
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MON_START", type="time", nullable=false)
     */
    private $monStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MON_END", type="time", nullable=false)
     */
    private $monEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_MON_24_HRS", type="boolean", nullable=false)
     */
    private $isMon24Hrs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TUE_START", type="time", nullable=false)
     */
    private $tueStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TUE_END", type="time", nullable=false)
     */
    private $tueEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_TUE_24_HRS", type="boolean", nullable=false)
     */
    private $isTue24Hrs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="WED_START", type="time", nullable=false)
     */
    private $wedStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="WED_END", type="time", nullable=false)
     */
    private $wedEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_WED_24_HRS", type="boolean", nullable=false)
     */
    private $isWed24Hrs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="THU_START", type="time", nullable=false)
     */
    private $thuStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="THU_END", type="time", nullable=false)
     */
    private $thuEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_THU_24_HRS", type="boolean", nullable=false)
     */
    private $isThu24Hrs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FRI_START", type="time", nullable=false)
     */
    private $friStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FRI_END", type="time", nullable=false)
     */
    private $friEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_FRI_24_HRS", type="boolean", nullable=false)
     */
    private $isFri24Hrs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SAT_START", type="time", nullable=false)
     */
    private $satStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SAT_END", type="time", nullable=false)
     */
    private $satEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_SAT_24_HRS", type="boolean", nullable=false)
     */
    private $isSat24Hrs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SUN_START", type="time", nullable=false)
     */
    private $sunStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SUN_END", type="time", nullable=false)
     */
    private $sunEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_SUN_24_HRS", type="boolean", nullable=false)
     */
    private $isSun24Hrs;
}