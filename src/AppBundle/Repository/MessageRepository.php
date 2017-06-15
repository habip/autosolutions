<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\Dialog;
use AppBundle\Entity\Message\ImageAttachment;
use AppBundle\Entity\User;
use AppBundle\Entity\Message\DialogParticipant;
use AppBundle\Entity\Message\MessageStatus;

class MessageRepository extends EntityRepository
{
    /**
     *
     * @param \AppBundle\Entity\Message\Dialog $dialog
     * @param int $page
     * @param \AppBundle\Entity\User $user
     * @param array $fields
     *
     * @return \AppBundle\Doctrine\Paginator
     */
    public function findByDialog($dialog, $page = null, $user = null, $fields = array())
    {
        $f = array();

        foreach ($fields as $field) {
            $matches = array();
            if (preg_match('/([a-z]+)(?:\.([a-z]+)(\.(\d+)?x(\d+)?)?)?/', $field, $matches)) {
                if (!isset($f[$matches[1]])) {
                    $f[$matches[1]] = array();
                }

                if (isset($matches[2]) && $matches[2]) {
                    if (!isset($f[$matches[1]][$matches[2]])) {
                        $f[$matches[1]][$matches[2]] = array();
                    }

                    if (isset($matches[4]) || isset($matches[5])) {
                        $f[$matches[1]][$matches[2]][] = array('width' => $matches[4], 'height' => $matches[5]);
                    }
                }
            }
        }

        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select(array('m', 'u', 'a'))
            ->from('AppBundleMessage:Message', 'm')
            ->leftJoin('m.user', 'u')
            ->leftJoin('m.attachments', 'a')
            ->where('identity(m.dialog) = :dialog')
            ->setParameter('dialog', $dialog->getId())
            ->orderBy('m.id', 'ASC');

        $paginator = new Paginator($qb, null === $page ? 1 : $page, 20);
        if (null === $page) {
            $paginator->setPage($paginator->getLastPage());
        }

        /**
         * Load attachments
         */
        $images = array();

        foreach ($paginator as $message) {
            foreach ($message->getAttachments() as $attachment) {
                if ($attachment instanceof PhotoAttachment) {
                    $images[] = $attachment->getId();
                }
            }
        }

        if (sizeof($images) > 0) {
            $t = 0;
            $iqb = $this->_em->createQueryBuilder();
            $iqb->select(array('partial a.{id}', 'i'))
                ->from('AppBundleMessage:ImageAttachment', 'a')
                ->leftJoin('a.image', 'i')
                ->where($iqb->expr()->in('a.id', $images));

            if (isset($f['attachments']) && isset($f['attachments']['image'])) {
                foreach ($f['attachments']['image'] as $size) {
                    $t++;
                    $cond = '';
                    if (is_numeric($size['width'])) {
                        $cond .= sprintf('t%s.thumbWidth = %s', $t, $size['width']);
                    }
                    if (is_numeric($size['height'])) {
                        $cond .= ($cond ? ' and ' : '') . sprintf('t%s.thumbHeight = %s', $t, $size['height']);
                    }
                    $cond .= sprintf(' and t%s.isCropped = %s', $t, is_numeric($size['width']) && is_numeric($size['height']) ? '1' : '0');
                    $iqb
                        ->addSelect('t' . $t)
                        ->leftJoin('i.thumbs', 't' . $t, 'with', $cond);
                }
            }

            if ($t == 0) {
                $iqb
                    ->addSelect('t')
                    ->leftJoin('i.thumbs', 't', 'with', 't.thumbWidth = 100 and t.thumbHeight = 100 and t.isCropped = 1');
            }

            $iqb->getQuery()->execute();
        }

        /**
         * Load statuses
         */
        if ($user != null) {
            /**
             * If user is not null load dialog participants statuses for authored messages
             * and own status for messages authored by others
             */
            $outIds = array();
            $inIds = array();
            foreach ($paginator as $message) {
                if ($message->getUser() == $user) {
                    $outIds[] = $message->getId();
                } else {
                    $inIds[] = $message->getId();
                }
            }
            if (sizeof($outIds) > 0) {
                $this->_em->createQuery('
                        select partial m.{id}, s
                        from AppBundleMessage:Message m
                        left join m.statuses s
                        where m.id in (:ids)')
                    ->setParameter('ids', $outIds)
                    ->getResult();
            }
            if (sizeof($inIds) > 0) {
                $this->_em->createQuery('
                        select partial m.{id}, s
                        from AppBundleMessage:Message m
                        left join m.statuses s with identity(s.user)=:user
                        where m.id in (:ids)')
                    ->setParameter('ids', $inIds)
                    ->setParameter('user', $user->getId())
                    ->getResult();
            }
        }

        return $paginator;
    }

    /**
     *
     * @param integer $id
     *
     * @return \AppBundle\Entity\Message\Message
     */
    public function findOneById($id)
    {
        return $this->_em->createQuery('
                select m, d, p, pu, s, su, mu
                from AppBundleMessage:Message m
                    join m.dialog d
                    join d.participants p
                    join p.user pu
                    left join m.statuses s
                    left join s.user su
                    left join m.user mu
                where m.id = :id
                ')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    /**
     *
     * @param Dialog $dialog
     * @param integer $stateId
     */
    public function findAfterStateId(Dialog $dialog, $stateId)
    {
        return $this->_em->createQuery('
                select m, u
                from AppBundleMessage:Message m
                    left join m.user u
                where m.stateId between :start and :end
                order by m.stateId')
            ->setParameter('start', $stateId)
            ->setParameter('end', $dialog->getLastStateId())
            ->getResult();
    }

    public function createRelated($entityName, $entityId, User $fromUser, User $toUser, $body)
    {
        $this->_em->beginTransaction();

        try {
            $ids = array($fromUser->getId(), $toUser->getId());
            sort($ids);
            $uniqueKey = sprintf('dialog_%s_%s_%s', $entityName, $entityId , implode('_', $ids));
            try {
                $dialog = $this->_em->getRepository('AppBundleMessage:Dialog')->findOneByUniqueKey($uniqueKey, true);
            } catch (\Exception $ex) {
                $dialog = null;
            }

            if ($dialog == null) {
                $dialog = new Dialog();
                $dialog->setRelatedEntity($entityName);
                $dialog->setRelatedEntityId($entityId);
                $dialog->setUser($fromUser);

                $participant1 = new DialogParticipant();
                $participant1->setUser($fromUser);
                //Ensure avatar loaded
                $this->_em->getRepository('AppBundle:User')->findOneById($fromUser->getId(), 'en', array('photo100x100'));
                $dialog->addParticipant($participant1);

                $participant2 = new DialogParticipant();
                $participant2->setUser($toUser);
                //Ensure avatar loaded
                $this->_em->getRepository('AppBundle:User')->findOneById($toUser->getId(), 'en', array('photo100x100'));
                $dialog->addParticipant($participant2);

                $this->_em->persist($dialog);

                $this->_em->persist($participant1);
                $this->_em->persist($participant2);
                $this->_em->flush();
            }

            $message = new Message();
            $message->setDialog($dialog);
            $message->setUser($fromUser);
            $message->setBody($body);
            $message->setGuid(uniqid());

            $this->_em->persist($message);

            $this->_em->flush();
            $this->_em->commit();
        } catch (\Exception $ex) {
            $this->_em->rollback();
            throw $ex;
        }

        return $message;
    }

    public function getUnreadCount(User $user)
    {
        return $this->_em->createQuery('
                select sum(p.unreadCount)
                from AppBundleMessage:DialogParticipant p
                where identity(p.user) = :user')
            ->setParameter('user', $user->getId())
            ->getSingleScalarResult();
    }

    public function getUnread(User $user)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select(array('m', 'u', 'a'))
            ->from('AppBundleMessage:Message', 'm')
            ->leftJoin('m.user', 'u')
            ->leftJoin('m.attachments', 'a')
            ->orderBy('m.id', 'ASC');

        $qb->leftJoin('m.statuses', 's', 'with', 'identity(s.user)=:user');

        $qb->where('s.status = :status and identity(m.dialog) in (
                    select d1.id
                    from AppBundleMessage:Dialog d1
                        left join d1.participants p1
                        left join p1.user u1
                    where u1.id = :user
                    )');

        $qb
            ->setParameter('status', MessageStatus::STATUS_NEW)
            ->setParameter('user', $user->getId());

        return $qb->getQuery()->getResult();
    }
}
