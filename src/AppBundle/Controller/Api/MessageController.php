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
use Doctrine\ORM\Query;
use AppBundle\Entity\Message\Dialog;
use AppBundle\Form\Type\Message\DialogFormType;
use AppBundle\Entity\Message\Message;
use AppBundle\Form\Type\Message\MessageFormType;
use Doctrine\ORM\EntityManager;

use Doctrine\DBAL\Migrations\AbortMigrationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Message\MessageStatus;
use AppBundle\Form\Type\Message\MessageStatusFormType;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use AppBundle\Entity\Message\DialogParticipant;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use AppBundle\Entity\User;
use Doctrine\DBAL\LockMode;

/**
 *
 * @Route("/api")
 *
 */
class MessageController extends FOSRestController
{
    /**
     * @Route("/dialog/{id}/", requirements={"id"="\d+"}, name="_dialog_get")
     * @View(serializerGroups={"Default","dialog","participants","profile","thumb100x100"})
     */
    public function dialogAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $dialog = $em->getRepository('AppBundleMessage:Dialog')->findOneById($id, true);

        if (null == $dialog) {
            throw new NotFoundHttpException();
        } else {
            if (false === $this->get('security.authorization_checker')->isGranted('view', $dialog)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            return $dialog;
        }
    }

    /**
     * @Route("/dialogs/", name="_dialog_list")
     * @Method({"GET"})
     * @QueryParam(name="entity", nullable=true)
     * @QueryParam(name="entityId", requirements="\d+", nullable=true)
     * @QueryParam(name="page", requirements="\d+", default="1")
     * @QueryParam(name="recordsPerPage", requirements="\d+", default="20")
     * @QueryParam(name="detailedOutput", requirements="true|false", nullable=true, description="Return detailed output for participants")
     * @QueryParam(name="participant", array=true, requirements="\d+", nullable=true, description="List of dialog participants to serach by")
     * @QueryParam(name="strict", requirements="true|false|0|1", nullable=true, description="Use list of participants as only or as ones that participate")
     */
    public function dialogsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $params = array();

        $detailedOutput = $request->query->has('detailedOutput') && $request->query->get('detailedOutput') == 'true';

        if ($request->query->has('entity')) {
            $params = array('entity' => $request->query->get('entity')?:'', 'entityId' => $request->query->get('entityId')?:'');

            $result =
                $em
                    ->getRepository('AppBundleMessage:Dialog')
                    ->findForUserByEntity(
                            $user->getId(),
                            $request->query->get('entity')?:null,
                            $request->query->get('entityId')?:null,
                            $request->query->get('page', 1),
                            $request->query->get('recordsPerPage', 20),
                            $detailedOutput
                            );
        } else {
            $result =
                $em
                    ->getRepository('AppBundleMessage:Dialog')
                    ->findForUser(
                            $user->getId(),
                            $request->query->get('page', 1),
                            $request->query->get('recordsPerPage', 20),
                            $detailedOutput
                            );
        }

        $headers = array();

        if ($result->haveToPaginate()) {
            $headers['Link'] = sprintf('<%s>; rel="last"', $this->generateUrl('_dialog_list', $params, true));

            $headers['Link'] = array(
                sprintf('<%s>; rel="current"', $this->generateUrl('_dialog_list', array_merge(array('page' => $result->getPage()), $params), true)),
                sprintf('<%s>; rel="last"', $this->generateUrl('_dialog_list', array_merge(array('page' => $result->getLastPage()), $params), true))
            );
            if ($result->getPage() > 1) {
                $headers['Link'][] = sprintf('<%s>; rel="prev"', $this->generateUrl('_dialog_list', array_merge(array('page' => $result->getPage()-1), $params), true));
            }
            if ($result->getPage() < $result->getLastPage()) {
                $headers['Link'][] = sprintf('<%s>; rel="next"', $this->generateUrl('_dialog_list', array_merge(array('page' => $result->getLastPage()+1), $params), true));
            }
        }

        //setting current user to show correct unread count
        foreach ($result as $dialog) {
            $dialog->setCurrentUser($user);
        }

        $view = \FOS\RestBundle\View\View::create($result->getResult(), 200, $headers);
        $groups = array('Default','dialog','participants','last_message','message','statuses');
        if ($detailedOutput) {
            $groups[] = 'profile';
            //$groups[] = 'thumb100x100';
        }
        $view->getSerializationContext()->setGroups($groups);

        return $view;
    }

    /**
     * @Route("/dialogs/", name="_dialog_create")
     * @Method({"POST"})
     */
    public function dialogCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $dialog = new Dialog();
        $dialog->setUser($user);

        return $this->processDialogForm($request, $dialog, $em);
    }

    private function processDialogForm(Request $request, Dialog $dialog, EntityManager $em)
    {
        $statusCode = $dialog->getId() ? 204 : 201;

        $originalParticipants = new ArrayCollection();

        foreach ($dialog->getParticipants() as $participant) {
            $originalParticipants[] = $participant;
        }

        $form = $this->createForm(new DialogFormType($em), $dialog);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->beginTransaction();
            $em->persist($dialog);

            foreach ($dialog->getParticipants() as $participant) {
                $em->persist($participant);
            }

            foreach ($originalParticipants as $participant) {
                if (false === $dialog->getParticipants()->contains($participant)) {
                    $em->remove($participant);
                }
            }

            if (false === $this->get('security.authorization_checker')->isGranted('create', $dialog)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            try {
                $em->flush();
                $em->commit();

                $headers = array('Location' =>
                        $this->generateUrl(
                                '_dialog_get', array('id' => $dialog->getId()),
                                true // absolute
                        ));

                $view = \FOS\RestBundle\View\View::create($dialog, $statusCode, $headers);
                $view->getSerializationContext()->setGroups(array('Default', 'dialog', 'participants', 'profile', 'thumb100x100'));

                return $view;
            } catch (UniqueConstraintViolationException $e) {
                $em->rollback();

                $dlg = $em
                    ->getRepository('AppBundleMessage:Dialog')
                    ->findOneByUniqueKey($dialog->getUniqueKey(), true);

                if (false === $this->get('security.authorization_checker')->isGranted('view', $dlg)) {
                    throw new AccessDeniedException('Unauthorized access!');
                }

                //setting current user to show correct unread count
                $dlg->setCurrentUser($dialog->getUser());

                $view = \FOS\RestBundle\View\View::create($dlg, 409);
                $view->getSerializationContext()->setGroups(array('Default', 'dialog', 'participants', 'profile', 'thumb100x100'));

                return $view;
            }
        }

        return \FOS\RestBundle\View\View::create($form, 400);
    }

    /**
     * Get messages list for dialog
     *
     * @Route("/dialog/{id}/messages/", name="_message_list")
     * @Method({"GET"})
     * @QueryParam(name="page", requirements="\d+", nullable=true)
     */
    public function messagesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $dialog = $em->getRepository('AppBundleMessage:Dialog')->findOneById($id);

        if (null == $dialog) {
            throw new NotFoundHttpException();
        } else {
            if (false === $this->get('security.authorization_checker')->isGranted('view', $dialog)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            $result = $em->getRepository('AppBundleMessage:Message')
                        ->findByDialog($dialog, $request->query->get('page'), $user, array('attachments.photo'));

            $headers = array();

            if ($result->haveToPaginate()) {
                $lastParams = array('id' => $dialog->getId(), 'page' => $result->getLastPage());
                $currParams = array('id' => $dialog->getId(), 'page' => $result->getPage());
                $prevParams = array('id' => $dialog->getId(), 'page' => $result->getPage()-1);
                $nextParams = array('id' => $dialog->getId(), 'page' => $result->getPage()+1);

                $headers['Link'] = array(
                    sprintf('<%s>; rel="current"', $this->generateUrl('_message_list', $currParams, true)),
                    sprintf('<%s>; rel="last"', $this->generateUrl('_message_list', $lastParams, true))
                );
                if ($result->getPage() > 1) {
                    $headers['Link'][] = sprintf('<%s>; rel="prev"', $this->generateUrl('_message_list', $prevParams, true));
                }
                if ($result->getPage() < $result->getLastPage()) {
                    $headers['Link'][] = sprintf('<%s>; rel="next"', $this->generateUrl('_message_list', $nextParams, true));
                }
            }

            $view = \FOS\RestBundle\View\View::create($result->getResult(), 200, $headers);
            $view->getSerializationContext()->setGroups(array('Default', 'message', 'statuses', 'attachments', 'thumb100x100'));

            return $view;
        }
    }

    /**
     * @Route("/dialog/{id}/messages/", name="_message_create")
     * @Method({"POST"})
     * @View(serializerGroups={"Default"})
     *
     * @param Request $request
     * @param int $id
     */
    public function messageCreateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $dialog = $em->getRepository('AppBundleMessage:Dialog')->findOneById($id);

        if (null == $dialog) {
            throw new NotFoundHttpException();
        } else {
            $message = $this->get('app.message_manager')->createMessage($dialog, $user);

            if (false === $this->get('security.authorization_checker')->isGranted('create', $message)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            return $this->processMessageForm($request, $message, $em);
        }
    }

    private function processMessageForm(Request $request, Message $message, EntityManager $em)
    {
        $statusCode = $message->getId() ? 204 : 201;

        $originalStatuses = new ArrayCollection();

        foreach ($message->getStatuses() as $status) {
            $originalStatuses[] = $status;
        }

        $form = $this->createForm(new MessageFormType($em), $message);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $headers = array();
            $em->beginTransaction();

            $em->persist($message);

            foreach ($message->getStatuses() as $status) {
                $em->persist($status);
            }

            foreach ($originalStatuses as $status) {
                if (false === $message->getStatuses()->contains($status)) {
                    $em->remove($status);
                }
            }

            $em->flush();
            $em->commit();

            $view = \FOS\RestBundle\View\View::create($message, $statusCode, $headers);
            $view->getSerializationContext()->setGroups(array('Default'));

            return $view;
        }

        return \FOS\RestBundle\View\View::create($form, 400);
    }

    /**
     * Get messages
     *
     * @Route("/messages/", name="_unread_message_list")
     * @Method({"GET"})
     * @QueryParam(name="page", requirements="\d+", nullable=true)
     */
    public function messagesListAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $dialog = $em->getRepository('AppBundleMessage:Dialog')->findOneById($id);

        if (null == $dialog) {
            throw new NotFoundHttpException();
        } else {
            if (false === $this->get('security.authorization_checker')->isGranted('view', $dialog)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            $result = $em->getRepository('AppBundleMessage:Message')
                        ->findByDialog($dialog, $request->query->get('page'), $user, array('attachments.photo'));

            $headers = array();

            if ($result->haveToPaginate()) {
                $lastParams = array('id' => $dialog->getId(), 'page' => $result->getLastPage());
                $currParams = array('id' => $dialog->getId(), 'page' => $result->getPage());
                $prevParams = array('id' => $dialog->getId(), 'page' => $result->getPage()-1);
                $nextParams = array('id' => $dialog->getId(), 'page' => $result->getPage()+1);

                $headers['Link'] = array(
                    sprintf('<%s>; rel="current"', $this->generateUrl('_message_list', $currParams, true)),
                    sprintf('<%s>; rel="last"', $this->generateUrl('_message_list', $lastParams, true))
                );
                if ($result->getPage() > 1) {
                    $headers['Link'][] = sprintf('<%s>; rel="prev"', $this->generateUrl('_message_list', $prevParams, true));
                }
                if ($result->getPage() < $result->getLastPage()) {
                    $headers['Link'][] = sprintf('<%s>; rel="next"', $this->generateUrl('_message_list', $nextParams, true));
                }
            }

            $view = \FOS\RestBundle\View\View::create($result->getResult(), 200, $headers);
            $view->getSerializationContext()->setGroups(array('Default', 'message', 'statuses', 'attachments', 'thumb100x100'));

            return $view;
        }
    }

    /**
     * Get message info
     *
     * @param Request $request
     * @param integer $id
     *
     * @Route("/message/{id}/", name="_message_get")
     * @Method({"GET"})
     * @View(serializerGroups={"Default", "message", "detailed"})
     */
    public function getMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('AppBundleMessage:Message')->findOneById($id);
        $this->get('mucomu_message.message_manager')->ensureStatuses($message);

        if (null == $message) {
            throw new NotFoundHttpException();
        } else {
            if (false === $this->get('security.authorization_checker')->isGranted('view', $message)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            return $message;
        }
    }

    /**
     * Get statuses for message
     *
     * @Route("/message/{id}/statuses/", name="_message_statuses")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function statusesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('AppBundleMessage:Message')->findOneById($id);
        $this->get('mucomu_message.message_manager')->ensureStatuses($message);

        if (null == $message) {
            throw new NotFoundHttpException();
        } else {
            if (false === $this->get('security.authorization_checker')->isGranted('view_statuses', $message)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            return $message->getStatuses();
        }
    }

    /**
     * Get status for message
     *
     * @Route("/message/{id}/status/", name="_message_status")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function statusAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('AppBundleMessage:Message')->findOneById($id);
        $this->get('mucomu_message.message_manager')->ensureStatuses($message);

        if (null == $message) {
            throw new NotFoundHttpException();
        } else {
            if (false === $this->get('security.authorization_checker')->isGranted('view', $message)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            return $message->getStatusForUser($this->get('security.token_storage')->getToken()->getUser());
        }
    }

    /**
     * Chanages message status
     *
     * @param Request $request
     * @param integer $id
     *
     * @Route("/message/{id}/status/", name="_message_status_change")
     * @Method({"PUT"})
     * @View(serializerGroups={"Default"})
     */
    public function changeStatusAction(Request $request, $id)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('AppBundleMessage:Message')->findOneById($id);

        if (null == $message) {
            throw new NotFoundHttpException();
        } else {
            $em->beginTransaction();
            $this->get('app.message_manager')->ensureStatuses($message);

            if (false === $this->get('security.authorization_checker')->isGranted('edit_status', $message)) {
                $em->rollback();
                throw new AccessDeniedException('Unauthorized access!');
            }

            if ( ($status = $message->getStatusForUser($this->get('security.token_storage')->getToken()->getUser())) == null ) {
                $em->rollback();
                throw new NotFoundHttpException();
            }

            return $this->processMessageStatusForm(
                    $request,
                    $status,
                    $em);
        }
    }

    private function processMessageStatusForm(Request $request, MessageStatus $messageStatus, EntityManager $em)
    {
        $statusCode = $messageStatus->getId() ? 200 : 201;

        if ($messageStatus->getId()) {
            $em->find('AppBundleMessage:MessageStatus', $messageStatus->getId(), LockMode::PESSIMISTIC_WRITE);
        }

        $form = $this->createForm(new MessageStatusFormType($em), $messageStatus, array('method' => $request->getMethod()));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($messageStatus);

            $em->flush();
            $em->commit();

            $headers = array();

            $view = \FOS\RestBundle\View\View::create($messageStatus, $statusCode, $headers);
            $view->getSerializationContext()
                ->setGroups(array('Default', 'message'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $em->rollback();

        return \FOS\RestBundle\View\View::create($form, 400);
    }

}