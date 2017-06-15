<?php
namespace AppBundle\Subscriber;

use AppBundle\AppEvents;
use AppBundle\Entity\Change;
use AppBundle\Entity\ChangeSubscriber;
use AppBundle\Event\ChangesEvent;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Event\OnFlushEventArgs;

abstract class AbstractLifecycleEventSubscriber implements EventSubscriber
{

    protected $insertedChanges = array();
    protected $pendingRelatedObjects = array();
    protected $changes = array();
    protected $changeMD;
    protected $subscriberMD;

    protected $dispatcher;

    public function getSubscribedEvents()
    {
        return array(
                Events::onFlush,
                Events::postPersist,
                Events::postFlush
        );
    }

    /**
     *
     * Checks if this listener is listening to changes of the object
     *
     * @return boolean
     */
    protected function isSubscribedTo($object, $action)
    {
        return false;
    }

    /**
     * Serializes given entity to writable format (JSON)
     *
     * @param mixed $entity
     */
    protected function serialize($entity, $action, $changedProperties = null)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Return list of users who see this entity changes
     *
     * @param mixed $entity
     */
    protected function getSubscribers($entity)
    {
        return array();
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $this->insertedChanges = [];

        $this->changeMD = $em->getClassMetadata('AppBundle\Entity\Change');
        $this->subscriberMD = $em->getClassMetadata('AppBundle\Entity\ChangeSubscriber');

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($this->isSubscribedTo($entity, Change::ACTION_CREATE)) {
                $this->createChange(Change::ACTION_CREATE, $entity, $em, $uow);
            }
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($this->isSubscribedTo($entity, Change::ACTION_UPDATE)) {
                $this->createChange(Change::ACTION_UPDATE, $entity, $em, $uow);
            }
        }
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($this->isSubscribedTo($entity, Change::ACTION_REMOVE)) {
                $this->createChange(Change::ACTION_REMOVE, $entity, $em, $uow);
            }
        }
    }

    public function postPersist(EventArgs $event)
    {
        /* @var $event \Doctrine\ORM\Event\LifecycleEventArgs */
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();
        $object = $event->getObject();
        $oid = spl_object_hash($object);

        if ($this->insertedChanges && array_key_exists($oid, $this->insertedChanges)) {
            $change = $this->insertedChanges[$oid];
            $changeMD = $this->changeMD;

            $id = $object->getId();
            $serialized = $this->serialize($object, Change::ACTION_CREATE);

            $changeMD->getReflectionProperty('objectId')->setValue($change, $id);
            $changeMD->getReflectionProperty('value')->setValue($change, $serialized);

            $uow->scheduleExtraUpdate($change, array(
                    'objectId' => array(0, $id),
                    'value' => array('', $serialized)
            ));

            $uow->setOriginalEntityProperty($oid, 'objectId', $id);
            $uow->setOriginalEntityProperty($oid, 'value', $serialized);
            unset($this->insertedChanges[$oid]);
        }

        if ($this->pendingRelatedObjects && array_key_exists($oid, $this->pendingRelatedObjects)) {
            $owner = $this->pendingRelatedObjects[$this->pendingRelatedObjects[$oid]];
            $i = array_search($oid, $owner[1]);
            array_splice($owner[1], $i, 1);

            // if all related objects are processed then reassign owner objects change
            if (sizeof($owner[1]) == 0) {
                $change = $owner[0];
                $changeMD = $this->changeMD;

                $id = $object->getId();
                $serialized = $this->serialize($owner[3], $owner[4], $owner[2]);

                $changeMD->getReflectionProperty('value')->setValue($change, $serialized);

                $uow->scheduleExtraUpdate($change, array(
                        'value' => array('', $serialized)
                ));

                $uow->setOriginalEntityProperty($oid, 'objectId', $id);
                $uow->setOriginalEntityProperty($oid, 'value', $serialized);
                unset($this->pendingRelatedObjects[$this->pendingRelatedObjects[$oid]]);
            }
            unset($this->pendingRelatedObjects[$oid]);
        }

    }

    public function postFlush(PostFlushEventArgs $event)
    {
        $this->dispatcher->dispatch(AppEvents::CHANGES_ADDED, new ChangesEvent(array_values($this->changes)));
        $this->changes = array();
    }

    /**
     *
     * @param string $action
     * @param object $entity
     * @param EntityManager $em
     * @param UnitOfWork $uow
     */
    protected function createChange($action, $entity, EntityManager $em, UnitOfWork $uow)
    {
        $changeMD = $this->changeMD;
        $subscriberMD = $this->subscriberMD;
        $metadata = $em->getClassMetadata(get_class($entity));

        $oid = spl_object_hash($entity);
        $change = new Change();
        $this->changes[$oid] = $change;
        $change->setAction($action);
        $change->setObjectClass($metadata->name);
        $change->setTimestamp(new \DateTime());

        if ($action === Change::ACTION_CREATE) {
            $this->insertedChanges[$oid] = $change;
            // use 0 temporarily
            $change->setObjectId(0);
            // use empty string temporarily
            $change->setValue('');
        } else {
            $change->setObjectId($entity->getId());
            $newValues = array();
            $incomplete = false;
            $voids = array();
            foreach ($uow->getEntityChangeSet($entity) as $field => $changes) {
                $value = $changes[1];
                $newValues[$field] = $value;
                if ($metadata->isSingleValuedAssociation($field) && $value && !$value->getId()) {
                    $incomplete = true;
                    $void = spl_object_hash($value);
                    $this->pendingRelatedObjects[$void] = $oid;
                    $voids[] = $void;
                }
            }
            if (!$incomplete) {
                $change->setValue($this->serialize($entity, $action, $newValues));
            } else {
                $change->setValue('');
                $this->pendingRelatedObjects[$oid] = array($change, $voids, $newValues, $entity, $action);
            }
        }

        $em->persist($change);
        $uow->computeChangeSet($changeMD, $change);

        $users = $this->getSubscribers($entity);

        foreach ($users as $user) {
            $subscriber = new ChangeSubscriber();
            $subscriber->setUser($user);
            $change->addSubscriber($subscriber);

            $em->persist($subscriber);
            $uow->computeChangeSet($subscriberMD, $subscriber);
        }
    }
}