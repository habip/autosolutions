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
use JMS\Serializer\Serializer;
use AppBundle\AppEvents;
use AppBundle\Event\ChangesEvent;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\CarOwnerRequest;
use AppBundle\Entity\Change;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Notification\Notification;
use Doctrine\ORM\Query\AST\InstanceOfExpression;

class CarOwnerRequestLifecycleEventSubscriber extends AbstractLifecycleEventSubscriber
{

    private $serializer;
    private $notificationLifecycleSubscriber;

    public function __construct(Serializer $serializer, EventDispatcherInterface $dispatcher, $subscriber)
    {
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
        $this->notificationLifecycleSubscriber = $subscriber;
    }

    public function getSubscribedEvents()
    {
        return array(
                Events::onFlush,
                Events::postPersist,
                Events::postFlush
        );
    }

    protected function isSubscribedTo($entity, $action)
    {
        if ($entity instanceof CarOwnerRequest && (Change::ACTION_CREATE == $action || Change::ACTION_UPDATE == $action)) {
            return true;
        }

        return false;
    }

    protected function getSubscribers($entity)
    {
        $users = array();
        if ($entity instanceof CarOwnerRequest) {
            /* @var $entity \AppBundle\Entity\CarOwnerRequest */
            if ($entity->getCarService()) {
                /* @var $employee \AppBundle\Entity\Employee */
                foreach ($entity->getCarService()->getEmployees() as $employee) {
                    $users[] = $employee->getUser();
                }
            }
        }
        return $users;
    }

    protected function serialize($entity, $action, $changedProperties = null)
    {
        $scontext = new SerializationContext();
        $wrap = array('action' => $action);

        if ($entity instanceof CarOwnerRequest) {
            $scontext
                ->setSerializeNull(true)
                ->setGroups(array('Default', 'schedule'));
            $wrap['car_owner_request'] = $entity;
        }
        return $this->serializer->serialize($wrap, 'json', $scontext);
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        parent::onFlush($event);

        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof CarOwnerRequest && ($changeSet = $uow->getEntityChangeSet($entity))
                    && isset($changeSet['status']) && $entity->getCarOwner()->getUser()) {
                $notification = new Notification();
                $notification
                    ->setRequest($entity)
                    ->setMessage(sprintf('Статус заявки сменился на "%s"', CarOwnerRequest::$statusNames[$entity->getStatus()]))
                    ->setUser($entity->getCarOwner()->getUser())
                ;

                $metaData = $em->getClassMetadata('AppBundle\Entity\Notification\Notification');
                $em->persist($notification);
                $uow->computeChangeSet($metaData, $notification);

                $this->notificationLifecycleSubscriber->onFlush($event);
            }
        }
    }

}