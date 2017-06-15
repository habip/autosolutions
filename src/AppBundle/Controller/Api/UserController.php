<?php
namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use AppBundle\Form\Type\UserFormType;
use libphonenumber\PhoneNumberUtil;

/**
 *
 * @Route("/api")
 *
 */
class UserController extends FOSRestController
{

    /**
     * @Route("/profile/", name="_api_get_profile")
     * @Method({"GET"})
     * @View(serializerGroups={"Default", "self", "profile", "thumb100x100"})
     */
    public function profileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $user = $em->getRepository('AppBundle:User')->findOneById($user->getId());

        return $user;
    }
    
    /**
     * @Route("/profile/", name="_api_set_profile")
     * @Method({"POST"})
     * @View(serializerGroups={"Default", "self", "profile", "thumb100x100"})
     */
    public function profileSetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user = $em->getRepository('AppBundle:User')->findOneById($user->getId());
        $carOwner = $user->getCarOwner();
        $carOwner->setFirstName($request->request->get('firstname'));
        $carOwner->setLastname($request->request->get('lastname'));
        $carOwner->setLocality($em->getRepository('AppBundle:Locality')->findOneById($request->request->get('locality')));
        $user->setPhone($this->get('libphonenumber.phone_number_util')->parse($request->request->get('phone'), PhoneNumberUtil::UNKNOWN_REGION));
        $user->setEmail($request->request->get('email'));
        //$em->persist($carOwner);
       ///$em->persist($user);
        //$em->flush();

        return $user;
    }

    /**
     * @Route("/profile/{id}", requirements={"id"="\d+"}, name="_api_get_user_profile")
     * @Method({"GET"})
     * @View(serializerGroups={"Default", "profile", "thumb100x100"})
     */
    public function userProfileAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $u = $em->getRepository('AppBundle:User')->findOneById($id);

        return $u;
    }


    /**
     *
     * @param Request $request
     * @param integer $userId
     * @param integer $id
     *
     * @Route("/changes/{id}")
     *
     */
    public function getUserChangesSince(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $result =  array();

        $changes = $em->getRepository('AppBundle:Change')->findChangesForUserSince($user->getId(), $id);

        /* @var $change \AppBundle\Entity\Change */
        foreach ($changes as $change) {
            $changeData = json_decode($change->getValue(), true);
            $changeData['id'] = $change->getGuid();
            $changeData['action'] = $change->getAction();

            $result[] = $changeData;
        }

        return $result;
    }

}