<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\CarOwner;

class CarOwnerEvent extends Event
{
    private $carOwner;
    private $locale;

    public function __construct(CarOwner $carOwner, $locale)
    {
        $this->carOwner = $carOwner;
        $this->locale = $locale;
    }

    /**
     * @return CarOwner
     */
    public function getCarOwner()
    {
        return $this->carOwner;
    }

    /**
     * get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

}
