<?php
namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Notification\Notification;


class NotificationController extends FOSRestController
{
    /**
     * @Route("/api/notifications/", name="_notification_list")
     * @Method({"GET"})
     */
    public function notificationListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $em->getRepository('AppBundleNotification:Notification')->getUnread($user);
    }

    /**
     * @Route("/api/notification/{id}/", requirements={"id"="\d+"}, name="_notification")
     * @Method({"POST"})
     */
    public function notificationStatusReadAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $notificationRepository = $em->getRepository('AppBundleNotification:Notification');
        /* @var $notification \AppBundle\Entity\Notification\Notification */
        $notification = $notificationRepository->findOneByIdForUser($user, $id);
        if ($notification->getRequest() && $notification->getRequest()->getReview()) {
            $notification->setStatus(Notification::STATUS_READ);

            $em->persist($notification);
            $em->flush();
        }
        return $notification;
    }

    /**
     * @Route("/api/notification/echo/",  name="_notification_echo")
     */
    public function notificationEcho(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $notification = new Notification();
        $notification->setUser($user);
        $notification->setMessage($request->query->get('body'));
        $notification->addFlag('show_modal');
        $notification->addFlag('no_clear');
        $notification->setRequest($user->getCarOwner()->getCarOwnerRequests()[0]);
        $em->persist($notification);
        $em->flush();

        $notification = new Notification();
        $notification->setUser($user);
        $notification->setMessage(rand(0,100));
        $notification->setRequest($user->getCarOwner()->getCarOwnerRequests()[0]);
        $em->persist($notification);
        $em->flush();
        return null;
    }

}