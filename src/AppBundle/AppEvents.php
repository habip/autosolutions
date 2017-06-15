<?php
namespace AppBundle;

final class AppEvents
{
    /**
     * The COMPANY_REGISTRATION_SUCCESS event occurs when the registration form is submitted successfully.
     */
    const COMPANY_REGISTRATION_SUCCESS = 'app.company_registration.success';

    /**
     * The CAR_OWNER_REGISTRATION_SUCCESS event occurs when the registration form is submitted successfully.
     */
    const CAR_OWNER_REGISTRATION_SUCCESS = 'app.car_owner_registration.success';

    /**
     * The CHANGES_ADDED event occurs when some entity generating Change was created or updated
     */
    const CHANGES_ADDED = 'mucomu_main.changes.added';

    const OFFLINE_MESSAGE = 'mucomu_message.offline_message';
}