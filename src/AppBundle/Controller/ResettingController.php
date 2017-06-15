<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\Type\ResettingFormType;
use AppBundle\Form\Type\UserCheckPhoneFormType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/resetting")
 */
class ResettingController extends Controller
{

    /**
     * @Route("/request/", name="_resetting_request")
     */
    public function requestAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $email = $request->request->get('email');

            try {
                $user = $this->get('app.entity_user_provider')->loadUserByUsername($email);
            } catch (\Exception $ex) {
                $user = null;
            }

            if ($user) {
                if ($user->getEmail() == $email) {
                    return $this->forward('AppBundle:Resetting:sendEmail', array('user' => $user));
                } else if ($this->get('libphonenumber.phone_number_util')->format($user->getPhone(), \libphonenumber\PhoneNumberFormat::E164) == $email) {
                    $this->get('app.sms_manager')->sendValidation($user);
                    $request->getSession()->set('resettingUser', $user);
                    return $this->redirect($this->generateUrl('_resetting_check_phone'));
                } else {
                    return $this->render(':Resetting:request.html.twig', array('invalid_email' => $email));
                }
            } else {
                return $this->render(':Resetting:request.html.twig', array('invalid_email' => $email));
            }
        } else {
            return $this->render(':Resetting:request.html.twig');
        }
    }

    private function generateToken() {
        $random = hash('sha256', uniqid(mt_rand(), true), true);
        return rtrim(strtr(base64_encode($random), '+/', '-_'), '=');
    }

    /**
     * @Route("/send-email/", name="_resetting_send_email")
     */
    public function sendEmailAction(Request $request, User $user)
    {
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->generateToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        $this->container->get('app.mailer.twig_swift')->sendResettingEmailMessage($user);

        return $this->redirect($this->generateUrl('_resetting_check_email', array('email' => $user->getEmail())));
    }

    /**
     * @Route("/check-email/", name="_resetting_check_email")
     */
    public function checkEmailAction(Request $request)
    {
        return $this->render(':Resetting:checkEmail.html.twig', array('email' => $request->get('email')));
    }

    /**
     * @Route("/reset/{token}/", name="_resetting_reset")
     */
    public function resetAction(Request $request, $token)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        if ($user = $repository->findOneBy(array('confirmationToken' => $token))) {

            $form = $this->createForm(new ResettingFormType(), $user);

            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
                    $user->setPassword($password);

                    $user->setConfirmationToken(null);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add(
                            'info',
                            'resetting.flash.password_changed'
                    );

                    return $this->redirect($this->generateUrl("_login"));
                } else {
                    return $this->render(':Resetting:reset.html.twig', array('form' => $form->createView()));
                }
            } else {
                return $this->render(':Resetting:reset.html.twig', array('form' => $form->createView()));
            }

        } else {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }
    }

    /**
     * @Route("/check-phone/", name="_resetting_check_phone")
     */
    public function checkPhoneAction(Request $request)
    {
        if ($user = $request->getSession()->get('resettingUser')) {
            $form = $this->createForm(new UserCheckPhoneFormType(), $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                return $this->redirect($this->generateUrl('_resetting_reset_password'));
            } else {
                return $this->render(':Resetting:checkPhone.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView()
                ));
            }
        } else {
            $this->redirect($this->generateUrl('_resetting_request'));
        }
    }

    /**
     * @Route("/resend-code/", name="_resetting_resend_code")
     */
    public function resendCodeAction(Request $request)
    {
        $session = $request->getSession();

        if ($user = $session->get('resettingUser')){
            $this->get('app.sms_manager')->sendValidation($user);

            return $this->redirect($this->generateUrl('_resetting_check_phone'));
        } else {
            return $this->redirect($this->generateUrl('_resetting_request'));
        }
    }

    /**
     * @Route("/reset/", name="_resetting_reset_password")
     */
    public function resetPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $request->getSession()->get('resettingUser');

        if ($user) {
            $form = $this->createForm(new ResettingFormType(), $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $user->getPlainPassword();
                $user = $em->getRepository('AppBundle:User')->find($user->getId());
                $user->setPlainPassword($plainPassword);
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
                $user->setPassword($password);

                $user->setConfirmationToken(null);
                $user->setIsActive(true);

                $em->persist($user);
                $em->flush();

                $request->getSession()->remove('resettingUser');

                return $this->redirect($this->generateUrl("_login"));
            } else {
                return $this->render(':Resetting:reset.html.twig', array('form' => $form->createView()));
            }
        } else {
            $this->redirect($this->generateUrl('_resetting_request'));
        }
    }
}