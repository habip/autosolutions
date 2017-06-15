<?php

namespace AppBundle\EntityManager;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Message\Dialog;
use AppBundle\Entity\User;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\MessageStatus;

class MessageManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var
     */
    protected $repository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->respository = $this->em->getRepository('AppBundleMessage:Message');
    }

    public function createMessage(Dialog $dialog, User $user)
    {
        $message = new Message();
        $message->setUser($user);
        $message->setDialog($dialog);

        foreach ($dialog->getParticipants() as $participant) {
            if ($participant->getUser() != $user) {
                $status = new MessageStatus();
                $status->setUser($participant->getUser());
                $message->addStatus($status);
            }
        }

        return $message;
    }

    public function ensureStatuses(Message $message)
    {
        $this->em->beginTransaction();
        $users = array();
        foreach ($message->getStatuses() as $status) {
            $users[$status->getUser()->getId()] = $status;
        }

        $count = 0;
        foreach ($message->getDialog()->getParticipants() as $participant) {
            if ($participant->getUser() != $message->getUser() && !isset($users[$participant->getUser()->getId()])) {
                $status = new MessageStatus();
                $status->setUser($participant->getUser());
                $message->addStatus($status);

                $this->em->persist($status);
                $count++;
            }
        }
        if ($count > 0) {
            $this->em->flush();
        }
        $this->em->commit();
    }
}