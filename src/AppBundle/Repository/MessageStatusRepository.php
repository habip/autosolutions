<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\User;
use AppBundle\Entity\Message\Dialog;

class MessageStatusRepository extends EntityRepository
{
    /**
     *
     * @param Message $message
     *
     * @return array
     */
    public function findByMessage(Message $message)
    {
        $query = $this->_em->createQuery('
                select m
                from AppBundleMessage:MessageStatus m
                where identity(m.message) = :message')
            ->setParameter('message', $message->getId());

        return $query->getResult();
    }

    /**
     * Returns changed message statuses in range [stateId, $dialog->lastStateId]
     *
     * If user is author of message he should see all changed statuses, otherwise only his status
     * messageStatus.user == user
     *
     * @param Dialog $dialog
     * @param integer $stateId
     * @param TUser $user
     */
    public function findAfterStateId(Dialog $dialog, $stateId, User $user)
    {
        return $this->_em->createQuery('
                select ms, u, m
                from AppBundleMessage:MessageStatus ms
                    left join ms.user u
                    left join ms.message m
                where ms.stateId between :start and :end
                    and (identity(ms.user) = :user or identity(m.user) = :user)
                order by ms.stateId')
            ->setParameter('start', $stateId)
            ->setParameter('end', $dialog->getLastStateId())
            ->setParameter('user', $user->getId())
            ->getResult();
    }
}