<?php
namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CarOwnerRequestFormType;
use AppBundle\Form\Type\UserCheckPhoneFormType;
use AppBundle\Form\Type\UserRegistrationFormType;

use AppBundle\Entity\Car;
use AppBundle\Entity\CarOwner;
use AppBundle\Entity\CarOwnerRequest;
use AppBundle\Entity\User;


/**
 *
 * @Route("/api")
 *
 */
class DefaultController extends FOSRestController
{
    /**
     * @Route("/get-token")
     */
    public function getTokenAction()
    {
        return new Response('', 401);
    }
    
    /**
     * @Route("/create-request/", name="_api_create_request")
     */
    public function createRequestAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $carOwnerRequest = new CarOwnerRequest();
        $carOwnerRequest->setCar(new Car());
        $carOwner = new CarOwner();
        $carOwner->setUser(new User());
        $carOwnerRequest->setCarOwner($carOwner);

        if ($request->query->get('carServiceId') && ($carService = $em->getRepository('AppBundle:CarService')->findOneById($request->query->get('carServiceId')))) {
            $carOwnerRequest->setCarService($carService);
        }

        $form = $this->createForm(new CarOwnerRequestFormType(), $carOwnerRequest, array(
                'type' => 'anon_create_request',
                'em' => $em,
                'csrf_protection' => false,
                'validation_groups' => array('Default', 'request')));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $carOwner = $carOwnerRequest->getCarOwner();
            $this->get('app.sms_manager')->sendValidation($carOwner->getUser());

            $view = \FOS\RestBundle\View\View::create($carOwnerRequest, 202);
            $request->getSession()->set('carOwnerRequest', $carOwnerRequest);
        } else {
            $view = \FOS\RestBundle\View\View::create($form, $request->isXmlHttpRequest() ? 400 : 200);
        }


        return $view;
    }
    

    /**
     * @Route("/create-request/check-phone/", name="_api_create_request_check_phone")
     */
    public function unregisteredUserRequestCreateCheckPhoneAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($carOwnerRequest = $request->getSession()->get('carOwnerRequest')) {

            $form = $this->createForm(new UserCheckPhoneFormType(), $carOwnerRequest->getCarOwner()->getUser(), array('csrf_protection' => false));
            $form->handleRequest($request);

            $view = \FOS\RestBundle\View\View::create(array(), 400);
            
            if ($form->isSubmitted() && $form->isValid()) {
                if (strpos($request->headers->get('Accept'), 'application/json') !== false) {
                    $view = \FOS\RestBundle\View\View::create(array(), 202);
                }
            }
            return $view;
        } else {
            return $this->redirect($this->generateUrl('_main'));
        }
    }

    /**
     * @Route("/create-request/resend-code/", name="_api_create_request_resend_code")
     */
    public function resendCodeAction(Request $request)
    {
        $session = $request->getSession();

        if ($carOwnerRequest = $session->get('carOwnerRequest')){
            $carOwner = $carOwnerRequest->getCarOwner();
            $this->get('app.sms_manager')->sendValidation($carOwner->getUser());

            $session->set('carOwnerRequest', $carOwnerRequest);

            if ($request->isXmlHttpRequest()) {
                $response = new Response(json_encode(array('status'=>'ok')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                return $this->redirect($this->generateUrl('_create_request_check_phone'));
            }
        } else {
            return $this->redirect($this->generateUrl('_main'));
        }
    }
    
    /**
     * @Route("/create-request/password/", name="_api_create_request_password")
     */
    public function unregisteredUserRequestCreatePasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (($carOwnerRequest = $request->getSession()->get('carOwnerRequest')) && $carOwnerRequest->getCarOwner()->getUser()->isCodeValid()) {

            $form = $this->createForm(new UserRegistrationFormType(), $carOwnerRequest->getCarOwner()->getUser(), array('email' => false, 'csrf_protection' => false));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->getRepository('AppBundle:CarOwnerRequest')->restoreFromSession($carOwnerRequest);

                $carOwner = $carOwnerRequest->getCarOwner();
                $user = $carOwner->getUser();
                $phoneString = $this->get('libphonenumber.phone_number_util')->format($user->getPhone(), \libphonenumber\PhoneNumberFormat::E164);
                $user->setUsername($phoneString);
                $carOwner->setNickname($carOwner->getFirstName() . ' ' . $carOwner->getLastName());
                $user->setIsActive(true);
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
                $user->setPassword($password);

                $carOwnerRequest->getCar()->setCarOwner($carOwner);
                $carOwner->setLocality($request->attributes->get('_locality'));

                $em->persist($user);
                $em->persist($carOwner);
                $em->persist($carOwnerRequest->getCar());
                $em->persist($carOwnerRequest);
                $em->flush();

                $this->get('app.notifier')->notifyRequest($carOwnerRequest);
                $this->get('app.sms_manager')->sendSms(
                    $this->get('libphonenumber.phone_number_util')->format($user->getPhone(), \libphonenumber\PhoneNumberFormat::E164),
                    sprintf('Спасибо что воспользовались нашим сервисом. Ваш пароль для доступа в систему: %s', $user->getPlainPassword())
                );

                $request->getSession()->getFlashBag()->add('car_owner_request', $carOwnerRequest->getId());

                $securityToken = new UsernamePasswordToken(
                        $user,
                        $user->getPassword(),
                        'secured_area',
                        $user->getRoles()
                );

                $this->get('security.token_storage')->setToken($securityToken);

                if (strpos($request->headers->get('Accept'), 'application/json') !== false) {
                    $view = \FOS\RestBundle\View\View::create(array(), 202);
                } else {
                    return $this->redirect($this->generateUrl('_create_request_success'));
                }
            } else {
                $view = \FOS\RestBundle\View\View::create($form, $request->isXmlHttpRequest() ? 400 : 200);

                $view
                    ->setTemplateVar('form')
                    ->setTemplate(':CarOwner:unregisteredUserRequestCreatePassword.html.twig')
                    ->setTemplateData(array('carOwnerRequest' => $carOwnerRequest));
            }

             //Enable CORS
            if ($request->headers->has('Origin')) {
                $view
                    ->setHeader('Access-Control-Allow-Origin', $request->headers->get('Origin'))
                    ->setHeader('Access-Control-Allow-Credentials', 'true');
            }

            return $view;
        } else {
            return $this->redirect($this->generateUrl('_main'));
        }
    }
}