<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageVoter extends AbstractVoter
{
    const CREATE = 'create';
    const VIEW = 'view';
    const VIEW_STATUSES = 'view_statuses';
    const EDIT_STATUS = 'edit_status';

    protected function getSupportedAttributes()
    {
        return array(
            self::CREATE,
            self::VIEW,
            self::VIEW_STATUSES,
            self::EDIT_STATUS
        );
    }

    protected function getSupportedClasses()
    {
        return array('AppBundle\Entity\Message\Message');
    }

    protected function isGranted($attribute, $message, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($attribute == self::CREATE && $message->getDialog()->isParticipant($user)) {
            return true;
        }

        if ($attribute == self::VIEW && $message->getDialog()->isParticipant($user)) {
            return true;
        }

        if ($attribute == self::VIEW_STATUSES && $message->getUser() == $user) {
            return true;
        }

        if ($attribute == self::EDIT_STATUS && $message->getDialog()->isParticipant($user)) {
            return true;
        }

        return false;
    }
}