<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;

class DialogRepository extends EntityRepository
{
    /**
     *
     * @param int $id
     * @return \AppBundle\Entity\Message\Dialog
     */
    public function findOneById($id, $detailedOutput = false)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('d, p, u')
            ->from('AppBundleMessage:Dialog', 'd')
            ->leftJoin('d.participants', 'p')
            ->leftJoin('p.user', 'u')
            ->where('d.id = :id')
            ->setParameter('id', $id);

        if ($detailedOutput) {
            $query
                ->addSelect('c, o, i, t')
                ->leftJoin('u.company', 'c')
                ->leftJoin('u.carOwner', 'o')
                ->leftJoin('u.image', 'i')
                ->leftJoin('i.thumbs', 't', 'with', 't.width = 100 and t.height = 100 and t.isCropped = 1');
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     *
     * @param integer $userId
     * @param integer $page
     * @param boolean $detailedOutput
     * @return \AppBundle\Doctrine\Paginator
     */
    public function findForUser($userId, $page = 1, $recordsPerPage = 20, $detailedOutput = false)
    {
        $query = $this->_em->createQueryBuilder()
                ->select('d, p, u, lm, lmu')
                ->from('AppBundleMessage:Dialog', 'd')
                ->leftJoin('d.participants', 'p')
                ->leftJoin('p.user', 'u')
                ->leftJoin('d.lastMessage', 'lm')
                ->leftJoin('lm.user', 'lmu')
                ->where('d.id in (
                    select d1.id
                    from AppBundleMessage:Dialog d1
                        left join d1.participants p1
                        left join p1.user u1
                    where u1.id = :user
                    )')
                ->orderBy('d.id', 'ASC')
            ->setParameter('user', $userId);

        if ($detailedOutput) {
            $query
                ->addSelect('c, o, i, t')
                ->leftJoin('u.company', 'c')
                ->leftJoin('u.carOwner', 'o')
                ->leftJoin('u.image', 'i')
                ->leftJoin('i.thumbs', 't', 'with', 't.width = 100 and t.height = 100 and t.isCropped = 1');
        }

        $paginator = new Paginator($query, $page, $recordsPerPage);

        // Load status for incoming messages
        $inIds = array();
        foreach ($paginator as $dialog) {
            if ($dialog->getLastMessage()!=null)
                if ($dialog->getLastMessage()->getUser()->getId() != $userId) {
                    $inIds[] = $dialog->getLastMessage()->getId();
                }
        }

        if (sizeof($inIds) > 0) {
            $this->_em->createQuery('
                    select partial m.{id}, s
                    from AppBundleMessage:Message m
                    left join m.statuses s with identity(s.user)=:user
                    where m.id in (:ids)')
                ->setParameter('ids', $inIds)
                ->setParameter('user', $userId)
                ->getResult();
        }

        return $paginator;
    }

    /**
     *
     * @param integer $userId
     * @param string $entity
     * @param integer $entityId
     * @param integer $page
     * @param boolean $detailedOutput
     * @return \AppBundle\Doctrine\Paginator
     */
    public function findForUserByEntity($userId, $entity, $entityId, $page = 1, $recordsPerPage = 20, $detailedOutput = false)
    {
        $qb = $this->_em->createQueryBuilder()
                ->select('d, p, u, lm, lmu')
                ->from('AppBundleMessage:Dialog', 'd')
                ->leftJoin('d.participants', 'p')
                ->leftJoin('p.user', 'u')
                ->leftJoin('d.lastMessage', 'lm')
                ->leftJoin('lm.user', 'lmu')
                ->orderBy('d.id', 'ASC');

        if ($detailedOutput) {
            $qb
                ->addSelect('c, o, i, t')
                ->leftJoin('u.company', 'c')
                ->leftJoin('u.carOwner', 'o')
                ->leftJoin('u.image', 'i')
                ->leftJoin('i.thumbs', 't', 'with', 't.width = 100 and t.height = 100 and t.isCropped = 1');
        }

        $qbInner = $this->_em->createQueryBuilder()
                ->select('d1.id')
                ->from('AppBundleMessage:Dialog', 'd1')
                ->leftJoin('d1.participants', 'p1')
                ->leftJoin('p1.user', 'u1')
                ->where('u1.id = :user');

        $qb->setParameter('user', $userId);

        if (null === $entity) {
            $qbInner->andWhere('d1.relatedEntity is NULL and d1.relatedEntityId is NULL');
        } else if (null !== $entity && null === $entityId) {
            $qbInner->andWhere('d1.relatedEntity = :entity');
            $qb->setParameter('entity', $entity);
        } else {
            $qbInner->andWhere('d1.relatedEntity = :entity and d1.relatedEntityId = :entityId');
            $qb
                ->setParameter('entity', $entity)
                ->setParameter('entityId', $entityId);
        }

        $qb->where($qb->expr()->in('d.id', $qbInner->getDql()));

        $paginator = new Paginator($qb, $page, $recordsPerPage);

        // Load status for incoming messages
        $inIds = array();
        foreach ($paginator as $dialog) {
            if ($dialog->getLastMessage()->getUser()->getId() != $userId) {
                $inIds[] = $dialog->getLastMessage()->getId();
            }
        }

        if (sizeof($inIds) > 0) {
            $this->_em->createQuery('
                    select partial m.{id}, s
                    from AppBundleMessage:Message m
                    left join m.statuses s with identity(s.user)=:user
                    where m.id in (:ids)')
                            ->setParameter('ids', $inIds)
                            ->setParameter('user', $userId)
                            ->getResult();
        }

        return $paginator;
    }

    public function findOneByUniqueKey($uniqueKey, $detailedOutput = false)
    {
        $qb = $this->_em->createQueryBuilder()
                ->select('d, p, u, lm, lmu')
                ->from('AppBundleMessage:Dialog', 'd')
                ->leftJoin('d.participants', 'p')
                ->leftJoin('p.user', 'u')
                ->leftJoin('d.lastMessage', 'lm')
                ->leftJoin('lm.user', 'lmu')
                ->where('d.uniqueKey = :key')
                ->setParameter('key', $uniqueKey);

        if ($detailedOutput) {
            $qb
                ->addSelect('c, o, i, t')
                ->leftJoin('u.company', 'c')
                ->leftJoin('u.carOwner', 'o')
                ->leftJoin('u.image', 'i')
                ->leftJoin('i.thumbs', 't', 'with', 't.width = 100 and t.height = 100 and t.isCropped = 1');
        }

        return $qb->getQuery()->getSingleResult();
    }

    public function findInterlocutors(User $user, $onlineOnly = false)
    {
        $onlineCond = ' and u1.isOnline = true';

        $query = $this->_em->createQuery('
                select u1
                from AppBundle:User u1
                where u1.id in (
                    select distinct u.id
                    from AppBundleMessage:Dialog d
                    join d.participants p
                    join d.participants p1
                    join p1.user u
                    where identity(p.user) = :user and identity(p1.user) <> :user
                )' . ($onlineOnly ? $onlineCond : ''))
            ->setParameter('user', $user->getId());

        return $query->getResult();
    }
}