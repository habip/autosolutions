<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewVoter extends AbstractVoter
{
    const CREATE = 'create';
    const RESPONSE = 'response';

    protected function getSupportedAttributes()
    {
        return array(self::CREATE, self::RESPONSE);
    }

    public function getSupportedClasses()
    {
        return array('AppBundle\Entity\Review');
    }

    protected function isGranted($attribute, $review, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* @var $review \AppBundle\Entity\Review */
        if ($attribute == self::CREATE && $review->getCarOwnerRequest()->getCarOwner()->getUser() == $user) {
            return true;
        }

        if ($attribute == self::RESPONSE && $review->getCarService()->getCompany()->getUser() == $user) {
            return true;
        }

        return false;
    }

}