<?php

namespace AppBundle\EventListener;

use AppBundle\AppEvents;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Event\CompanyEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Event\CarOwnerEvent;

class EmailConfirmationListener implements EventSubscriberInterface
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    private function generateToken() {
        $random = hash('sha256', uniqid(mt_rand(), true), true);
        return rtrim(strtr(base64_encode($random), '+/', '-_'), '=');
    }

    public static function getSubscribedEvents()
    {
        return array(
            AppEvents::COMPANY_REGISTRATION_SUCCESS => 'onCompanyRegistrationSuccess',
            AppEvents::CAR_OWNER_REGISTRATION_SUCCESS => 'onCarOwnerRegistrationSuccess',
        );
    }

    public function onCompanyRegistrationSuccess(CompanyEvent $event)
    {
        $company = $event->getCompany();
        $user = $company->getUser();

        $user->setIsActive(0);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->generateToken());
        }

        $this->mailer->sendCompanyConfirmationEmailMessage($user, $event->getLocale());
    }

    public function onCarOwnerRegistrationSuccess(CarOwnerEvent $event)
    {
        $carOwner = $event->getCarOwner();
        $user = $carOwner->getUser();

        $user->setIsActive(0);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->generateToken());
        }

        $this->mailer->sendCarOwnerConfirmationEmailMessage($user, $event->getLocale());
    }


}