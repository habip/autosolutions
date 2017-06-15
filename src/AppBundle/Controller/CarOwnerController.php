<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\CarOwner;
use AppBundle\Entity\Car;
use AppBundle\Entity\CarOwnerRequest;
use AppBundle\Entity\User;
use AppBundle\AppEvents;
use AppBundle\Event\CarOwnerEvent;
use AppBundle\Form\Type\CarOwnerRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query;
use AppBundle\Form\Type\CarOwnerFormType;
use libphonenumber\PhoneNumberUtil;
use AppBundle\Form\Type\UserStartRegistrationFormType;
use AppBundle\Form\Type\UserCheckPhoneFormType;
use AppBundle\Form\Type\CarFormType;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use AppBundle\Form\Type\CarOwnerRequestFormType;

/**
 *
 * @Route("/car-owner")
 *
 */
class CarOwnerController extends FOSRestController
{

    /**
     * @Route("/registration/", name="_car_owner_registration")
     */
    public function startRegistrationAction(Request $request)
    {
        $carOwner = new CarOwner();
        $user = new User();
        $user->setCarOwner($carOwner);

        $form = $this->createForm(new UserStartRegistrationFormType(), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();

            $this->get('app.sms_manager')->sendValidation($user);

            $session->set('carOwner', $carOwner);

            return $this->redirect($this->generateUrl('_car_owner_check_phone'));
        } else {
            return $this->render(':CarOwner:startRegistration.html.twig', array(
                    'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/registration/resend-code/", name="_car_owner_resend_code")
     */
    public function resendCodeAction(Request $request)
    {
        $session = $request->getSession();

        if ($carOwner = $session->get('carOwner')){
            $this->get('app.sms_manager')->sendValidation($carOwner->getUser());

            $session->set('carOwner', $carOwner);

            if ($request->isXmlHttpRequest()) {
                $response = new Response(json_encode(array('status'=>'ok')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                return $this->redirect($this->generateUrl('_car_owner_check_phone'));
            }
        } else {
            return $this->redirect($this->generateUrl('_car_owner_registration'));
        }
    }

    /**
     * @Route("/registration/check-phone/", name="_car_owner_check_phone")
     */
    public function registrationCheckPhoneAction(Request $request)
    {
        $session = $request->getSession();

        if ($carOwner = $session->get('carOwner')) {
            $form = $this->createForm(new UserCheckPhoneFormType(), $carOwner->getUser());

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                return $this->redirect($this->generateUrl('_car_owner_registration_data'));
            } else {
                return $this->render(':CarOwner:checkPhone.html.twig', array(
                        'carOwner' => $carOwner,
                        'form' => $form->createView()
                ));
            }
        } else {
            return $this->redirect($this->generateUrl('_car_owner_registration'));
        }
    }

    /**
     * @Route("/registration/data/", name="_car_owner_registration_data")
     */
    public function registrationAction(Request $request)
    {
        $session = $request->getSession();
        $dispatcher = $this->get('event_dispatcher');
        $em = $this->getDoctrine()->getManager();

        /* @var $carOwner \AppBundle\Entity\CarOwner */
        if (($carOwner = $session->get('carOwner')) && $carOwner->getUser()->isCodeValid()) {
            if ($carOwner->getLocality()) {
                $carOwner->setLocality($em->getRepository('AppBundle:Locality')->find($carOwner->getLocality()->getId()));
            }
            $user = $carOwner->getUser();

            $form = $this
                ->createForm(new CarOwnerRegistrationFormType(), $carOwner, array('em' => $em))
                ->add('save', 'submit', array('label' => 'registration.save'));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
                $user->setPassword($password);
                $phoneString = $this->get('libphonenumber.phone_number_util')->format($carOwner->getUser()->getPhone(), \libphonenumber\PhoneNumberFormat::E164);
                $user->setUserName($phoneString);
                $user->setIsActive(true);

                $em->getConnection()->beginTransaction();

                try {
                    $em->persist($user);
                    $em->persist($carOwner);

                    $em->flush();

                    $event = new CarOwnerEvent($carOwner, $request->getLocale());
                    $dispatcher->dispatch(AppEvents::CAR_OWNER_REGISTRATION_SUCCESS, $event);

                    $em->persist($carOwner);
                    $em->flush();

                    $em->getConnection()->commit();
                } catch(Exception $ex) {
                    $em->getConnection()->rollback();
                    throw $ex;
                }

                $session->set('registered_car_owner_email', $user->getEmail());

                $securityToken = new UsernamePasswordToken(
                        $user,
                        $user->getPassword(),
                        'secured_area',
                        $user->getRoles()
                );

                $this->get('security.token_storage')->setToken($securityToken);

                return $this->redirect($this->generateUrl('_car_owner_main'));
            } else {
                return $this->render(':CarOwner:registration.html.twig', array(
                        'form' => $form->createView()
                ));
            }
        } else {
            return $this->redirect($this->generateUrl('_car_owner_registration'));
        }
    }

    /**
     * @Route("/registration/resend-confirmation/", name="_car_owner_resend_confirmation")
     */
    public function resendConfirmationAction(Request $request)
    {
        if (($request->getSession()->get(SecurityContext::LAST_USERNAME)
                && ($u = $this->get('app.entity_user_provider')->loadUserByUsername($request->getSession()->get(SecurityContext::LAST_USERNAME))))
                || ($request->query->get('email') && ($u = $this->get('app.entity_user_provider')->loadUserByUsername($request->query->get('email'))))) {

            if ($u->getType() == User::TYPE_CAR_OWNER && !$u->getIsActive() && null !== $u->getConfirmationToken()) {
                $dispatcher = $this->get('event_dispatcher');

                $event = new CarOwnerEvent($u->getCarOwner(), $request->getLocale());
                $dispatcher->dispatch(AppEvents::CAR_OWNER_REGISTRATION_SUCCESS, $event);

                return $this->render(":CarOwner:confirmationResended.html.twig", array('user' => $u));
            } else {
                return $this->redirect($this->generateUrl('_car_owner_registration'));
            }
        } else {
            throw new NotFoundHttpException(sprintf('The user not found'));
        }
    }

    /**
     * @Route("/registration/check-email/", name="_car_owner_check_email")
     */
    public function checkEmailAction(Request $request){

        $email = $request->getSession()->get('registered_car_owner_email');

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        if ($user = $repository->findOneBy(array('email' => $email))) {
            return $this->render(':CarOwner:checkEmail.html.twig', array(
                    'user' => $user
            ));
        } else {
            return $this->redirect($this->generateUrl('_car_owner_registration'));
        }

    }

    /**
     * @Route("/registration/confirm/{token}", name="_car_owner_registration_confirm")
     */
    public function confirmAction(Request $request, $token)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        if ($user = $repository->findOneBy(array('confirmationToken' => $token))) {
            if (!$user->getIsActive()) {
                //never delete token
                //$user->setConfirmationToken(null);
                $user->setIsActive(true);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

            }

            $securityToken = new UsernamePasswordToken(
                    $user,
                    $user->getPassword(),
                    'car_owner_secured_area',
                    $user->getRoles()
            );

            $this->get('security.context')->setToken($securityToken);

            $this->get('session')->getFlashBag()->add(
                    'info',
                    'registration.confirmed'
            );

            return $this->redirect($this->generateUrl('_car_owner_main'));
        } else {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }
    }

    /**
     * @Route("/", name="_car_owner_main")
     */
    public function mainAction()
    {
        return $this->render(":CarOwner:main.html.twig");
    }


    /**
     * @Route("/help/", name="_car_owner_help")
     */
    public function helpAction(Request $request)
    {
        return $this->render(":CarOwner:help.html.twig");
    }

    /**
     * @Route("/profile/", name="_car_owner_profile")
     */
    public function profileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $carOwner = $user->getCarOwner();


        $cars = $carOwner->getCars();

        $form = $this->createForm(new CarOwnerFormType(), $carOwner, array('em' => $em));
        $form->handleRequest($request);

        if ($form->isValid()){

            $em->persist($carOwner);
            $em->flush();

            return $this->redirect($this->get('router')->generate('_car_owner_profile'));
        } else {
            return $this->render(":CarOwner:profile.html.twig", array(
                'carOwner' => $carOwner,
                'form' => $form->createView(),
                'cars' => $cars
            ));
        }
    }


    /**
     * @Route("/profile/edit/", name="_car_owner_profile_edit")
     */
    public function profileEditAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $carOwner = $user->getCarOwner();

        $form = $this->createForm(new CarOwnerFormType(), $carOwner, array('em' => $em));
        $form->handleRequest($request);

        if ($form->isValid()){

            $em->persist($carOwner);
            $em->flush();

            return $this->redirect($this->get('router')->generate('_car_owner_profile'));
        } else {

            return $this->render(':CarOwner:profileEdit.html.twig', array(
                'carOwner' => $carOwner,
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/requests/", name="_car_owner_requests")
     */
    public function requestsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $carOwnerRequests = $em->getRepository('AppBundle:CarOwnerRequest')->findByUserForPeriod($user, null, $request->query->get('sortByCar'));

        return $this->render(':CarOwner:requests.html.twig', array(
            'carOwnerRequests' => $carOwnerRequests,
            'serviceRepo' => $em->getRepository('AppBundle:Service')
        ));
    }

    /**
     * @Route("/requests/create/", name="_car_owner_request_create")
     */
    public function requestCreateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $carOwner = $this->get('security.context')->getToken()->getUser()->getCarOwner();

        $reasonId = 1;

        if ($request->query->get('carService') && $request->query->get('reason')
                && ($carService = $em->getRepository('AppBundle:CarService')->findOneById($request->query->get('carService')))
                && ($reason = $em->getRepository('AppBundle:ServiceReason')->findOneById($request->query->get('reason'))) ) {

            $request->getSession()->remove('carOwnerRequest');

            $carOwnerRequest = new CarOwnerRequest();
            if ($request->query->get('car') && ($car = $em->getRepository('AppBundle:Car')->findOneById($request->query->get('car')))) {
                $carOwnerRequest->setCar($car);
            }
            if ($request->query->get('services') && ($services = $em->getRepository('AppBundle:Service')->findBy(array('id' => $request->query->get('services'))))) {
                $carOwnerRequest->setServices(new ArrayCollection($services));
            }
            $carOwnerRequest->setCarOwner($carOwner);
            $carOwnerRequest->setCarService($carService);
            $carOwnerRequest->addReason($reason);
            $reasonId = $carOwnerRequest->getReasons()[0]->getId();
        } else if ($carOwnerRequest = $request->getSession()->get('carOwnerRequest')) {
            $carOwnerRequest->setCarOwner($carOwner);
            $em->getRepository('AppBundle:CarOwnerRequest')->restoreFromSession($carOwnerRequest);
            $carService = $carOwnerRequest->getCarService();
            if (count($carOwnerRequest->getReasons())>0)
                $reasonId = $carOwnerRequest->getReasons()->first()->getId();
        } else {
            return $this->redirect($this->generateUrl('_main'));
        }

        $brands = $em->getRepository('AppBundle:Brand')->findAll();
        $services = $em->createQuery('select s, g from AppBundle:Service s left join s.group g where s.deletedAt is null and identity(g.reason) = :reason order by g.position, g.name, s.position, s.name')->setParameter('reason', $reasonId)->getResult();

        $options = array('em' => $em, 'type' => 'user_create_request', 'carOwner' => $carOwner, 'validation_groups' => array('Default', 'request'));

        if (!$carOwnerRequest->getCar()->getId()) {
            $carOwnerRequest->getCar()->setCarOwner($carOwner);
            $em->persist($carOwnerRequest->getCar());
            $em->flush();
        }

        $form = $this->createForm(new CarOwnerRequestFormType(), $carOwnerRequest, $options);

        $form->handleRequest($request);

        $request->getSession()->set('carOwnerRequest', $carOwnerRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->remove('carOwnerRequest');

            $em->persist($carOwnerRequest);
            $em->flush();
            $this->get('app.notifier')->notifyRequest($carOwnerRequest);

            return $this->redirect($this->generateUrl('_car_owner_requests'));
        } else {
            $scheduleRepo = $em->getRepository('AppBundle:CarServiceSchedule');

            $start = new \DateTime();
            $start->setTime(0, 0, 0);
            $end = clone($start);
            $end->add(new \DateInterval(sprintf('P%sD', $carService->getRecordingDaysAhead())));

            $schedule = $scheduleRepo->findCurrentByCarService($carService, $start, $end);

            return $this->render(':CarOwner:requestCreate.html.twig', array(
                'form' => $form->createView(),
                'carService' => $carService,
                'carOwnerRequest' => $carOwnerRequest,
                'brands' => $brands,
                'services' => $services,
                'serviceGroups' => $em->getRepository('AppBundle:ServiceGroup')->findAll(),
                'serviceReasons' => $em->getRepository('AppBundle:ServiceReason')->findAll(),
                'schedule' => $schedule,
                'scheduleRepo' => $scheduleRepo
            ));
        }

    }

    /**
     * @Route("/request/{id}/cancel/", requirements={"id"="\d+"}, name="_cancel_car_owner_request")
     */
    public function requestCancelAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        /* @var $coRequest \AppBundle\Entity\CarOwnerRequest */
        if (($coRequest = $em->getRepository('AppBundle:CarOwnerRequest')->find($id))
                && $coRequest->isCancelable()) {
            $coRequest->setStatus(CarOwnerRequest::STATUS_CANCELED);
            $em->persist($coRequest);
            $em->flush();
            return $this->redirect($this->generateUrl('_car_owner_requests'));
        } else {
            throw new NotFoundHttpException('Заявка не найдена');
        }
    }

    /**
     * @Route("/cars/", name="_car_owner_cars")
     */
    public function carsAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $carOwner = $this->get('security.context')->getToken()->getUser()->getCarOwner();

        $cars = $em->getRepository('AppBundle:Car')->findByCarOwner($carOwner, array('car.images.thumbs[275x155]'));

        return $this->render(':CarOwner:cars.html.twig', array(
            'cars' => $cars
        ));
    }

    /**
     * @Route("/car/{id}/edit/", requirements={"id"="\d+"}, name="_car_owner_car_edit")
     */
    public function carEditAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $carOwner = $user->getCarOwner();

        $car = $em->getRepository('AppBundle:Car')->findOneById($id, array('car.images'));

        $origImages = new ArrayCollection();
        foreach ($car->getImages() as $image) {
            $origImages[] = $image;
        }

        $form = $this->createForm(new CarFormType(), $car, array('em' => $em, 'images' => true));
        $form->add('submit', 'submit', array('label' => 'Сохранить'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            foreach ($origImages as $image) {
                if (false === $car->getImages()->contains($image)) {
                    $em->getRepository('AppBundle:Image')->remove($image);
                }
            }

            $em->persist($car);
            $em->flush();

            $view = \FOS\RestBundle\View\View::create($car, 200);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        } else {
            $view = \FOS\RestBundle\View\View::create($form, $request->isXmlHttpRequest() ? 400 : 200);

            if (!$request->isXmlHttpRequest()) {
                $view
                    ->setTemplate(':CarOwner:cars.html.twig')
                    ->setTemplateVar('form')
                    ->setTemplateData(array(
                        'cars' => $em->getRepository('AppBundle:Car')->findByCarOwner($carOwner, array('car.images.thumbs[275x155]')),
                    ));
            }

            return $view;
        }
    }

    /**
     * @Route("/car/{id}/delete/", requirements={"id"="\d+"}, name="_car_owner_car_delete")
     */
    public function carDeleteAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $carOwner = $user->getCarOwner();

        try {
            $car = $em->getRepository('AppBundle:Car')->findOneById($id);
            $car->setIsDeleted(true);

            $em->persist($car);
            $em->flush();
        } catch(NoResultException $ex) {
            throw new NotFoundHttpException();
        }

        return $this->redirect($this->generateUrl('_car_owner_cars'));
    }

}