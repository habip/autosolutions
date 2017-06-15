<?php

namespace AppBundle\Mailer;

use AppBundle\Entity\User;

interface MailerInterface
{
    /**
     * Send an email to a user to confirm the account creation
     *
     * @param User $user
     * @param string $locale
     *
     * @return void
     */
    public function sendCompanyConfirmationEmailMessage(User $user, $locale);

    /**
     * Send an email to a user to confirm the password reset
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function sendCompanyResettingEmailMessage(User $user);

    /**
     * Send an email to a user to confirm the account creation
     *
     * @param User $user
     * @param string $locale
     *
     * @return void
     */
    public function sendCarOwnerConfirmationEmailMessage(User $user, $locale);

    /**
     * Send an email to a user to confirm the password reset
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function sendCarOwnerResettingEmailMessage(User $user);
}
