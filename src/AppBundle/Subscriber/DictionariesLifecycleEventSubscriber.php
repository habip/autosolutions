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
use AppBundle\Entity\Change;
use AppBundle\Entity\ChangeSubscriber;
use AppBundle\AppEvents;
use AppBundle\Event\ChangesEvent;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\ServiceReason;
use AppBundle\Entity\ServiceGroup;
use AppBundle\Entity\Service;
use AppBundle\Serializer\Exclusion\PropertyListExclusionStrategy;

class DictionariesLifecycleEventSubscriber extends AbstractLifecycleEventSubscriber
{

    private $serializer;

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
        if ($entity instanceof ServiceReason || $entity instanceof ServiceGroup || $entity instanceof Service) {
            return true;
        }

        return false;
    }

    protected function serialize($entity, $action, $changedProperties = null)
    {
        $scontext = new SerializationContext();
        $wrap = array('action' => $action);

        if ($changedProperties) {
            $scontext->addExclusionStrategy(new PropertyListExclusionStrategy(array_keys($changedProperties)));
        }

        if ($entity instanceof ServiceReason) {
            $scontext
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));
            $wrap['serviceReason'] = $entity;
        } else if ($entity instanceof ServiceGroup) {
            $scontext
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));
            $wrap['serviceGroup'] = $entity;
        } else if ($entity instanceof Service) {
            $scontext
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));
            $wrap['service'] = $entity;
        }

        return $this->serializer->serialize($wrap, 'json', $scontext);
    }

}