<?php
namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Notification\Notification;

class NotificationRepository extends EntityRepository
{

    public function getUnreadCount(User $user)
    {
        return $this->_em->createQuery('
                select count(n.id)
                from AppBundleNotification:Notification n
                where identity(n.user) = :user and n.status = :status')
            ->setParameter('user', $user->getId())
            ->setParameter('status', Notification::STATUS_NEW)
            ->getSingleScalarResult();
    }

    public function getUnread(User $user, $startDate = null)
    {
        $start = null;
        if ($startDate) {
            if (is_string($startDate)) {
                try {
                    $start = new \DateTime($startDate);
                } catch (\Exception $ex) {
                }
            } else if ($startDate instanceof \DateTime) {
                $start = $startDate;
            }
        }

        $qb = $this->_em->createQueryBuilder()
            ->select(array('n'))
            ->from('AppBundleNotification:Notification', 'n')
            ->where('identity(n.user) = :user and n.status = :status')
            ->orderBy('n.createdTimestamp', 'DESC')
            ->setParameter('user', $user->getId())
            ->setParameter('status', Notification::STATUS_NEW)
        ;

        if ($start) {
            $qb
                ->andWhere('n.createdTimestamp >= :start')
                ->setParameter('start', $start->format('Y-m-d H:i:s'));
        }

        return $qb->getQuery()->getResult();
    }

    public function findPreviousUnread(Notification $notification)
    {
        return $this->_em->createQuery('
                select n
                from AppBundleNotification:Notification n
                where n.createdTimestamp < :time
                    and identity(n.user) = :user
                    and identity(n.request) = :request
                    and n.status = :status
                ')
            ->setParameter('time', $notification->getCreatedTimestamp()->format('Y-m-d H:i:s'))
            ->setParameter('user', $notification->getUser()->getId())
            ->setParameter('request', $notification->getRequest()->getId())
            ->setParameter('status', Notification::STATUS_NEW)
            ->getResult();
    }

    public function findOneByIdForUser(User $user, $id)
    {
        return $this->_em->createQuery('
                select n
                from AppBundleNotification:Notification n
                where identity(n.user) = :user and n.id = :id
                order by n.createdTimestamp desc')
            ->setParameter('user', $user->getId())
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    public function findAllForUser(User $user)
    {
        return $this->_em->createQuery('
                select n
                from AppBundleNotification:Notification n
                where identity(n.user) = :user
                order by n.createdTimestamp desc')
            ->setParameter('user', $user->getId());
    }

}