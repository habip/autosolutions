<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\Dialog;

class DialogParticipantRepository extends EntityRepository
{

    /**
     *
     * @param Dialog $dialog
     * @param integer $stateId
     */
    public function findAfterStateId(Dialog $dialog, $stateId)
    {
        return $this->_em->createQuery('
                select p, u
                from AppBundleMessage:DialogParticipant p
                    left join p.user u
                where p.stateId between :start and :end
                order by p.stateId')
            ->setParameter('start', $stateId)
            ->setParameter('end', $dialog->getLastStateId())
            ->getResult();
    }
}