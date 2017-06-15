<?php

namespace AppBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Event\PostFlushEventArgs;
use JMS\Serializer\Serializer;
use AppBundle\Entity\Notification\Notification;
use AppBundle\Entity\Change;
use AppBundle\Entity\ChangeSubscriber;
use AppBundle\AppEvents;
use AppBundle\Event\ChangesEvent;
use AppBundle\Event\OfflineMessagesEvent;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificationLifecycleEventSubscriber extends AbstractLifecycleEventSubscriber
{

    private $serializer;

    public function __construct(Serializer $serializer, EventDispatcherInterface $dispatcher)
    {
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
    }

    protected function isSubscribedTo($object, $action)
    {
        if ($object instanceof Notification && Change::ACTION_CREATE) {
            return true;
        }

        return false;
    }

    protected function getSubscribers($entity)
    {
        $users = array();
        if ($entity instanceof Notification) {
            /* @var $entity \AppBundle\Entity\Notification\Notification */
            if ($entity->getRequest() && $entity->getRequest()->getCarOwner() && $entity->getRequest()->getCarOwner()->getUser()) {
                $users[] = $entity->getRequest()->getCarOwner()->getUser();
            }
        }
        return $users;
    }

    protected function serialize($entity, $action, $changedProperties = null)
    {
        $scontext = new SerializationContext();
        $wrap = array('action' => $action);

        if ($entity instanceof Notification) {
            $scontext
                ->setGroups(array('Default', 'notification', 'thumb100x100'));
            $wrap['notification'] = $entity;
        }
        return $this->serializer->serialize($wrap, 'json', $scontext);
    }

}