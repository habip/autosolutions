<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\Company;

class CompanyEvent extends Event
{
    private $company;
    private $locale;

    public function __construct(Company $company, $locale)
    {
        $this->company = $company;
        $this->locale = $locale;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
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
