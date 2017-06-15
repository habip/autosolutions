<?php
namespace AppBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Event\PostFlushEventArgs;
use AppBundle\Entity\Billing\Balance;
use AppBundle\Entity\Billing\OrderedService;
use AppBundle\Entity\Billing\Charge;
use AppBundle\Exception\NotEnoughFunds;
use AppBundle\Entity\User;
use AppBundle\Entity\Billing\SubscribedService;

class BalanceLifecycleEventSubscriber implements EventSubscriber
{

    private $changes = array();
    private $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function getSubscribedEvents()
    {
        return array(
                Events::onFlush,
                Events::postFlush
        );
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $chargeMD = $em->getClassMetadata('AppBundle\Entity\Billing\Charge');
        $balanceMD = $em->getClassMetadata('AppBundle\Entity\Billing\Balance');

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Balance) {
                /* @var $entity \AppBundle\Entity\Billing\Balance */
                $user = $entity->getUser();

                // If it is income try to activate services waiting for payment
                if ($entity->getPayment()) {
                    $this->logger->debug(sprintf('try to activate services waiting for payment, sum: %s', $entity->getPayment()->getSum()));
                    $remains = $entity->getPayment()->getSum();

                    // If sum of payment distributed by invoices
                    $this->logger->debug(sprintf('there is %s invoices associated with this payment', $entity->getPayment()->getInvoices()->count()));
                    if ($entity->getPayment()->getInvoices()->count() > 0) {
                        /* @var $payedInvoice \AppBundle\Entity\Billing\PaymentInvoice */
                        foreach ($entity->getPayment()->getInvoices() as $payedInvoice) {
                            $remains =- $payedInvoice->getSum();
                            // If invoice fully payed
                            if ($payedInvoice->getSum() == $payedInvoice->getInvoice()->getSum()) {
                                $service = $payedInvoice->getInvoice()->getOrderedService();
                                try {
                                    $this->charge($service, $user, $em, $uow, $chargeMD, $balanceMD);
                                } catch (NotEnoughFunds $ex) {
                                    $this->logger->debug(sprintf('not enough funds charging invoice #%s', $payedInvoice->getInvoice()->getId()));
                                }
                            }
                        }
                    }

                    // If there remains undistributed sum
                    $this->logger->debug(sprintf('there is %s remains after charging invoices', $remains));
                    if ($remains > 0) {
                        $waitingServices = $em->getRepository('AppBundleBilling:OrderedService')->findWaitingPayment($user);

                        $this->logger->debug(sprintf('there is %s services waiting payment', sizeof($waitingServices)));

                        /* @var $service \AppBundle\Entity\Billing\OrderedService */
                        /* @var $balance \AppBundle\Entity\Billing\Balance */
                        foreach ($waitingServices as $service) {
                            try {
                                $this->charge($service, $user, $em, $uow, $chargeMD, $balanceMD);
                            } catch (NotEnoughFunds $ex) {
                                $this->logger->debug(sprintf('not enough funds charging ordered service #%s', $service->getId()));
                            }
                        }
                    }
                }
            }
        }
    }

    private function charge(OrderedService $service, User $user, EntityManager $em, UnitOfWork $uow, ClassMetadata $chargeMD, ClassMetadata $balanceMD)
    {
        $userBalance = $user->getBalance();
        $this->logger->debug(sprintf('balanace %s before charge', $user->getBalance()));
        $balance = $em->getRepository('AppBundleBilling:OrderedService')->charge($service, $user);
        $this->logger->debug(sprintf('balanace %s after charge', $user->getBalance()));

        $uow->computeChangeSet($chargeMD, $balance->getCharge());
        $uow->computeChangeSet($balanceMD, $balance);
        $this->logger->debug(sprintf('scheduling extra balance %s', $user->getBalance()));
        $uow->scheduleExtraUpdate($user, array('balance' => array($userBalance, $user->getBalance())));
        $uow->scheduleExtraUpdate($service, array('status' => array(OrderedService::STATUS_WAITING_PAYMENT, OrderedService::STATUS_PAYED)));
        if ($service->getSubscribedService()) {
            $uow->scheduleExtraUpdate($service->getSubscribedService(), array('status' => array($service->getSubscribedService()->getStatus(), SubscribedService::STATUS_ACTIVE)));

            $service->getSubscribedService()->setOrderedService($service);
            $uow->computeChangeSet($em->getClassMetadata('AppBundle\Entity\Billing\SubscribedService'), $service->getSubscribedService());
        }

        /**
         * Если дата начала предостовлении услуги раньше текущего времени, оно должно изменится на текущее время
         * поскольку дата начала предостовления услуги не может быть раньше даты списания
        */
        $now = new \DateTime();
        if ($service->getStartDate() <= $now) {
            $start = clone $now;
            $oldVal = $service->getStartDate();
            $service->setStartDate($start);
            $uow->scheduleExtraUpdate($service, array('startDate' => array($oldVal, $start)));

            if ($service->getPrice()->getPeriod()) {
                $oldVal = $service->getEndDate();
                $service->setEndDate($now->add(new \DateInterval($service->getPrice()->getPeriod())));
                $uow->scheduleExtraUpdate($service, array('endDate' => array($oldVal, $service->getEndDate())));
            }
        }

        return $balance;
    }

    public function postFlush(PostFlushEventArgs $event)
    {
    }

}