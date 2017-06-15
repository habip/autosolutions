<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DialogVoter extends AbstractVoter
{
    const CREATE = 'create';
    const VIEW = 'view';

    protected function getSupportedAttributes()
    {
        return array(self::CREATE, self::VIEW);
    }

    public function getSupportedClasses()
    {
        return array('AppBundle\Entity\Message\Dialog');
    }

    protected function isGranted($attribute, $dialog, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($attribute == self::CREATE && $dialog->isParticipant($user)) {
            return true;
        }

        if ($attribute == self::VIEW && $dialog->isParticipant($user)) {
            return true;
        }

        return false;
    }

}