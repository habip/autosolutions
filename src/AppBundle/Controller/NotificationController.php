<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Notification\Notification;
use AppBundle\Entity\CarOwnerRequest;

/**
 * @Route("/car-owner", options={"expose"=true})
 */
class NotificationController extends FOSRestController
{
    /**
     * @Route("/notifications/", name="_car_owner_notifications")
     * @Method({"GET"})
     */
    public function notificationListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($request->query->get('status', 'all') == 'unread') {
            $defaultDate = (new \DateTime())->sub(new \DateInterval('P1D'));
            $notifications = $em->getRepository('AppBundleNotification:Notification')->getUnread($user, $request->query->get('start_date'));
        } else {
            $notifications = $em->getRepository('AppBundleNotification:Notification')->findAllForUser($user)->getResult();
        }

        $view =
            \FOS\RestBundle\View\View::create($notifications)
                ->setTemplate(':CarOwner:notifications.html.twig')
                ->setTemplateVar('notifications')
        ;

        return $view;
    }

    /**
     * @Route("/notification/{id}/", requirements={"id"="\d+"}, name="_car_owner_notification")
     * @Method({"POST"})
     */
    public function notificationStatusReadAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $notificationRepository = $em->getRepository('AppBundleNotification:Notification');
        /* @var $notification \AppBundle\Entity\Notification\Notification */
        $notification = $notificationRepository->findOneByIdForUser($user, $id);
        if ($notification->getRequest()
                && ($notification->getStatus() == CarOwnerRequest::STATUS_DONE && $notification->getRequest()->getReview())
                || $notification->getStatus() != CarOwnerRequest::STATUS_DONE) {

            foreach ($notificationRepository->findPreviousUnread($notification) as $n) {
                $n->setStatus(Notification::STATUS_READ);
                $em->persist($n);
            }

            $notification->setStatus(Notification::STATUS_READ);

            $em->persist($notification);
            $em->flush();
        }
        return $notification;
    }

}