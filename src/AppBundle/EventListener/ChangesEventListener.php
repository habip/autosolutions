<?php
namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Notifier\Notifier;
use AppBundle\AppEvents;
use AppBundle\Entity\Change;
use AppBundle\Event\ChangesEvent;

class ChangesEventListener implements EventSubscriberInterface
{
    private $notifier;

    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public static function getSubscribedEvents()
    {
        return array(
                AppEvents::CHANGES_ADDED => 'onChanges',
        );
    }

    public function onChanges(ChangesEvent $event)
    {
        /* @var $change \AppBundle\Entity\Change */
        foreach ($event->getChanges() as $change) {
            $this->notifier->send($change->getSubscribersIds(), $change->getValue(), $change->getGuid(), $change->getAction());
        }
    }

}