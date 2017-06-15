<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Billing\OrderedService;
use AppBundle\Entity\User;
use AppBundle\Entity\Billing\Service;
use AppBundle\Entity\Billing\Balance;
use AppBundle\Entity\Billing\Charge;
use AppBundle\Exception\NotEnoughFunds;

class OrderedServiceRepository extends EntityRepository
{
    /**
     * @param $user \AppBundle\Entity\User
     *
     * @return array
     */
    public function findWaitingPayment(User $user)
    {
        return
            $this->_em->createQuery('
                    select os, s
                    from AppBundleBilling:OrderedService os
                        join os.service s
                    where identity(os.user) = :user and os.status in (:status)
                    order by os.createdTimestamp')
                ->setParameter('user', $user->getId())
                ->setParameter('status', array(OrderedService::STATUS_NEW, OrderedService::STATUS_WAITING_PAYMENT))
                ->getResult();
    }

    public function findLast(User $user, Service $service, $status = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select(array('os', 's'))
            ->from('AppBundleBilling:OrderedService', 'os')
            ->join('os.service', 's')
            ->where('identity(os.user) = :user and identity(os.service) = :service')
            ->orderBy('os.createdTimestamp', 'desc')
            ->setParameter('user', $user->getId())
            ->setParameter('service', $service->getId())
            ->setMaxResults(1)
        ;

        if (null !== $status) {
            if (is_array($status)) {
                $qb->andWhere($qb->expr()->in('os.status', ':status'));
            } else {
                $qb->andWhere('os.status = :status');
            }
            $qb->setParameter('status', $status);
        }

        return $qb
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     *
     * To charge for the service ordered by the user if enough funds on the balance sheet
     *
     * @param OrderedService $service
     * @param User $user
     *
     * @return Balance
     */
    public function charge(OrderedService $service, User $user)
    {
        $price = $service->getSubscribedService()->getPrice()->getPrice();
        if ($price <= $user->getBalance()) {
            $charge = new Charge();
            $charge->setOrderedService($service);
            $charge->setUser($user);
            $this->_em->persist($charge);
            $balance = new Balance();
            $balance->setCharge($charge);
            $balance->setUser($user);
            $balance->setExpense($price);
            $this->_em->persist($balance);

            return $balance;
        } else {
            throw new NotEnoughFunds();
        }
    }

}
