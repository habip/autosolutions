<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CarOwnerRequestVoter extends AbstractVoter
{
    const SCHEDULE = 'schedule';

    protected function getSupportedAttributes()
    {
        return array(self::SCHEDULE);
    }

    public function getSupportedClasses()
    {
        return array('AppBundle\Entity\CarOwnerRequest');
    }

    protected function isGranted($attribute, $carOwnerRequest, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* @var $carOwnerRequest \AppBundle\Entity\CarOwnerRequest */
        if ($attribute == self::SCHEDULE) {
            return true;
        }

        return false;
    }

}