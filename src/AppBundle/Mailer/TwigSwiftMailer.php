<?php

namespace AppBundle\Mailer;

use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class TwigSwiftMailer implements MailerInterface
{
    protected $mailer;
    protected $router;
    protected $twig;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, \Twig_Environment $twig, array $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->parameters = $parameters;
    }

    public function sendCompanyConfirmationEmailMessage(User $user, $locale)
    {
        $template = $this->parameters['template']['confirmation'];
        $url = $this->router->generate('_company_registration_confirm', array('locale' => $locale, 'token' => $user->getConfirmationToken()), true);

        $context = array(
            'user' => $user,
            'confirmationUrl' => $url
        );

        $this->sendMessage($template, $context, $this->parameters['from_email']['confirmation'], $user->getEmail());
    }

    public function sendCompanyResettingEmailMessage(User $user)
    {
        $template = $this->parameters['template']['resetting'];
        $url = $this->router->generate('_resetting_reset', array('token' => $user->getConfirmationToken()), true);

        $context = array(
            'user' => $user,
            'confirmationUrl' => $url
        );

        $this->sendMessage($template, $context, $this->parameters['from_email']['resetting'], $user->getEmail());
    }

    public function sendCarOwnerConfirmationEmailMessage(User $user, $locale)
    {
        $template = $this->parameters['template']['confirmation'];
        $url = $this->router->generate('_car_owner_registration_confirm', array('locale' => $locale, 'token' => $user->getConfirmationToken()), true);

        $context = array(
                'user' => $user,
                'confirmationUrl' => $url
        );

        $this->sendMessage($template, $context, $this->parameters['from_email']['confirmation'], $user->getEmail());
    }

    public function sendCarOwnerResettingEmailMessage(User $user)
    {
        $template = $this->parameters['template']['resetting'];
        $url = $this->router->generate('_resetting_reset', array('token' => $user->getConfirmationToken()), true);

        $context = array(
                'user' => $user,
                'confirmationUrl' => $url
        );

        $this->sendMessage($template, $context, $this->parameters['from_email']['resetting'], $user->getEmail());
    }

    public function sendResettingEmailMessage(User $user)
    {
        $template = $this->parameters['template']['resetting'];
        $url = $this->router->generate('_resetting_reset', array('token' => $user->getConfirmationToken()), true);

        $context = array(
                'user' => $user,
                'confirmationUrl' => $url
        );

        $this->sendMessage($template, $context, $this->parameters['from_email']['resetting'], $user->getEmail());
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }

}
