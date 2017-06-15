<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CarServiceScheduleVoter extends AbstractVoter
{
    const SCHEDULE = 'schedule';
    const DELETE = 'delete';

    protected function getSupportedAttributes()
    {
        return array(self::SCHEDULE, self::DELETE);
    }

    public function getSupportedClasses()
    {
        return array('AppBundle\Entity\CarServiceSchedule');
    }

    protected function isGranted($attribute, $carServiceSchedule, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* TODO Add some logic!!! */
        /* @var $carOwnerRequest \AppBundle\Entity\CarServiceSchedule */
        if ($attribute == self::SCHEDULE) {
            return true;
        } else if ($attribute == self::DELETE) {
            return true;
        }

        return false;
    }

}