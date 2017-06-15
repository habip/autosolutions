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
use AppBundle\Entity\Message\Dialog;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\MessageStatus;
use AppBundle\Entity\Message\DialogParticipant;
use AppBundle\Entity\Change;
use AppBundle\Entity\ChangeSubscriber;
use AppBundle\AppEvents;
use AppBundle\Event\ChangesEvent;
use AppBundle\Event\OfflineMessagesEvent;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MessageLifecycleEventSubscriber extends AbstractLifecycleEventSubscriber
{

    private $serializer;

    private $newOfflineMessages = array();

    public function __construct(Serializer $serializer, EventDispatcherInterface $dispatcher)
    {
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
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
        if (Change::ACTION_CREATE == $action && ($entity instanceof Message || $entity instanceof Dialog
                || ($entity instanceof DialogParticipant && $entity->getDialog()->getId()))) {
            return true;
        } else if (Change::ACTION_UPDATE == $action && $entity instanceof MessageStatus) {
            return true;
        }

        return false;
    }

    public function postPersist(EventArgs $event)
    {
        parent::postPersist($event);

        $object = $event->getObject();

        if ($object instanceof Message) {
            /* @var $participant \AppBundle\Entity\Message\DialogParticipant */
            foreach ($object->getDialog()->getParticipants() as $participant) {
                if ($participant->getUser() != $object->getUser() && !$participant->getUser()->getIsOnline()) {
                    $this->newOfflineMessages[] = array($participant->getUser(), $object);
                }
            }
        }

    }

    public function postFlush(PostFlushEventArgs $event)
    {
        parent::postFlush($event);
        $this->dispatcher->dispatch(AppEvents::OFFLINE_MESSAGE, new OfflineMessagesEvent($this->newOfflineMessages));
        $this->newOfflineMessages = array();
    }

    protected function getSubscribers($entity)
    {
        $users = array();
        if ($entity instanceof Message) {
            /* @var $participant \AppBundle\Entity\Message\DialogParticipant */
            foreach ($entity->getDialog()->getParticipants() as $participant) {
                $users[] = $participant->getUser();
            }
        } else if ($entity instanceof MessageStatus) {
            $users[] = $entity->getMessage()->getUser();
            $users[] = $entity->getUser();
        } else if ($entity instanceof Dialog) {
            /* @var $participant \AppBundle\Entity\Message\DialogParticipant */
            foreach ($entity->getParticipants() as $participant) {
                $users[] = $participant->getUser();
            }
        } else if ($entity instanceof DialogParticipant) {
            /* @var $participant \AppBundle\Entity\Message\DialogParticipant */
            foreach ($entity->getDialog()->getParticipants() as $participant) {
                $users[] = $participant->getUser();
            }
        }
        return $users;
    }

    protected function serialize($entity, $action, $changedProperties = null)
    {
        $scontext = new SerializationContext();
        $wrap = array('action' => $action);

        if ($entity instanceof Message) {
            $scontext
                ->setGroups(array('Default', 'message', 'attachments', 'thumb100x100'));
            $wrap['message'] = $entity;
        } else if ($entity instanceof MessageStatus) {
            $scontext
                ->setGroups(array('Default'));
            $wrap['messageStatus'] = $entity;
        } else if ($entity instanceof Dialog) {
            $scontext
                ->setGroups(array('Default', 'dialog', 'participants', 'profile', 'thumb100x100'));
            $wrap['dialog'] = $entity;
        } else if ($entity instanceof DialogParticipant) {
            $scontext
                ->setGroups(array('Default', 'profile', 'thumb100x100'));
            $wrap['dialogParticipant'] = $entity;
        }
        return $this->serializer->serialize($wrap, 'json', $scontext);
    }

}