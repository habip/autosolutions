<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocalityListener implements EventSubscriberInterface
{
    private $doctrine;
    private $defaultLocality;

    public function __construct($doctrine, $defaultLocality = 8)
    {
        $this->doctrine = $doctrine;
        $this->defaultLocality = $defaultLocality;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($locality = $request->request->get('locality')) {
            $l = $this->doctrine->getManager()->getRepository('AppBundle:Locality')->findOneById($locality);
            if ($l) {
                $request->getSession()->set('_locality', $locality);
                $request->attributes->set('_locality', $l);
            }
        } else if ($request->getSession()->has('_locality')) {
            $l = $this->doctrine->getManager()->getRepository('AppBundle:Locality')->findOneById($request->getSession()->get('_locality'));
            if ($l) {
                $request->attributes->set('_locality', $l);
            }
        } else {
            $l = $this->doctrine->getManager()->getRepository('AppBundle:Locality')->findOneById($this->defaultLocality);
            $request->attributes->set('_locality', $l);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}