<?php
namespace AppBundle\Controller;

use AppBundle\Entity\CarOwnerRequest;
use AppBundle\Entity\CarService;
use AppBundle\Entity\CarServiceSchedule;
use AppBundle\Form\Type\CarOwnerRequestScheduleFormType;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @Route("/company/schedule")
 *
 */
class CarServiceScheduleController extends FOSRestController
{
    /**
     * @Method({"GET"})
     * @Route("/car-services/{carServiceId}/date/{date}/", name="_schedule_get")
     * @ParamConverter("carService", options={"id" = "carServiceId"})
     */
    public function getScheduleAction(Request $request, CarService $carService, $date = null)
    {
        $em = $this->getDoctrine()->getManager();

        $carOwnerRequests = $em->getRepository('AppBundle:CarOwnerRequest')->findBy(array('carService'=>$carService, 'status'=>CarOwnerRequest::STATUS_NEW));

        if (null === $date) {
            $date = new \DateTime();
            $date->setTime(0, 0, 0);
        }

        $scheduleRepo = $em->getRepository('AppBundle:CarServiceSchedule');

        $schedule = $scheduleRepo->findCurrentByCarService($carService, $date);

        $services = $em->getRepository('AppBundle:Service')->findAll(Query::HYDRATE_ARRAY);

        $view = \FOS\RestBundle\View\View::create($schedule)
            ->setTemplate(':Schedule:carServiceSchedule.html.twig')
            ->setTemplateVar('schedule')
            ->setTemplateData(array(
                'carService' => $carService,
                'services' => $services,
                'servicesProvided' => $em->getRepository('AppBundle:Service')->findByCompany($carService->getCompany()),
                'serviceGroups' => $em->getRepository('AppBundle:ServiceGroup')->findAll(),
                'date' => $date,
                'repository' => $scheduleRepo,
                'carOwnerRequests' => $carOwnerRequests
            ));

        return $view;
    }


    /**
     * @Method({"POST"})
     * @Route("/car-services/{carServiceId}/shedule/", name="_schedule_create")
     * @ParamConverter("carService", options={"id" = "carServiceId"})
     */
    public function createScheduleRecordAction(Request $request, CarService $carService)
    {
        $em = $this->getDoctrine()->getManager();

        $schedule = new CarServiceSchedule();
        $schedule->setCarService($carService);

        $form = $this->createForm(new CarServiceScheduleFormType(), $schedule, array('em' => $em, 'csrf_protection' => false));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->beginTransaction();
            //Lock Car service record
            $em->find('AppBundle:CarService', $schedule->getCarService()->getId(), LockMode::PESSIMISTIC_WRITE);

            if ($form->isValid()) {
                $scheduleItems = $em->getRepository('AppBundle:CarServiceSchedule')->findCurrentByCarService(
                        $schedule->getCarService(),
                        $schedule->getStartTime(),
                        $schedule->getEndTime());

                if (null == $schedule->getPost()) {
                    $post = $this->get('app.scheduler')->findFreePost($schedule->getCarService(), $scheduleItems);
                    $schedule->setPost($post);
                }

                $em->persist($schedule);

                $carOwnerRequest = $schedule->getCarOwnerRequest();

                $carOwnerRequest
                    ->setEntryTime($schedule->getStartTime())
                    ->setExitTime($schedule->getEndTime())
                    ->setPost($schedule->getPost());

                $carOwnerRequest->setStatus(CarOwnerRequest::STATUS_ASSIGN);
                if (null == $carOwnerRequest->getId()) {
                    $carOwnerRequest->setCarOwnerDate($schedule->getStartTime());
                    $carOwner = $carOwnerRequest->getCarOwner();
                    $em->persist($carOwnerRequest);
                    $em->persist($carOwner);
                    $carOwnerRequest->getCar()->setCarOwner($carOwner);
                    $em->persist($carOwnerRequest->getCar());
                } else {
                    $em->persist($carOwnerRequest);
                }

                if (false === $this->get('security.authorization_checker')->isGranted('schedule', $schedule)) {
                    throw new AccessDeniedException('Unauthorized access!');
                }

                $em->flush();
                $em->commit();

                $view = \FOS\RestBundle\View\View::create($schedule, 200);
                $view->getSerializationContext()->setGroups(array('Default'));

                return $view;
            }
        }

        return \FOS\RestBundle\View\View::create($form, 400);
    }

    /**
     * @Method({"PUT"})
     * @Route("/car-services/{carServiceId}/shedule/{sheduleId}", name="_schedule_edit")
     * @ParamConverter("carService", options={"id" = "carServiceId"})
     */
    public function editScheduleAction(Request $request, CarService $carService, $sheduleId)
    {
        $em = $this->getDoctrine()->getManager();
        $em->beginTransaction();

        $schedule = $em->getRepository('AppBundle:CarServiceSchedule')->find($sheduleId);

        $form = $this->createForm(new CarServiceScheduleFormType(), $schedule, array('em' => $em, 'method' => 'PUT',  'csrf_protection' => false));
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($carOwnerRequest = $schedule->getCarOwnerRequest()) {
                $carOwnerRequest
                    ->setEntryTime($schedule->getStartTime())
                    ->setExitTime($schedule->getEndTime())
                    ->setPost($schedule->getPost());

                $em->persist($carOwnerRequest);
            }

            $em->persist($schedule);

            if (false === $this->get('security.authorization_checker')->isGranted('schedule', $schedule)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            $em->flush();
            $em->commit();

            $view = \FOS\RestBundle\View\View::create($schedule, 201);
            $view->getSerializationContext()->setGroups(array('Default'));

            return $view;
        } else {
            $em->rollback();
            return \FOS\RestBundle\View\View::create($form, 400);
        }
    }

    /**
     * @Method({"DELETE"})
     * @Route("/car-services/{carServiceId}/shedule/{id}/", name="_schedule_delete")
     * @ParamConverter("carService", options={"id" = "carServiceId"})
     */
    public function deleteScheduleAction(Request $request, CarService $carService, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $schedule = $em->getRepository('AppBundle:CarServiceSchedule')->find($id);

        if (false === $this->get('security.authorization_checker')->isGranted('delete', $schedule)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $em->remove($schedule);
        $em->flush();

        $view = \FOS\RestBundle\View\View::create(null, 204);

        return $view;
    }

    /**
     * @Method({"GET"})
     * @Route("/car-services/{carServiceId}/requests/{date}", name="_schedule_get_requests", requirements={"date"="\d{4}-\d{2}-\d{2}"})
     * @ParamConverter("carService", options={"id" = "carServiceId"})
     */
    public function getRequestsAction(Request $request, CarService $carService, $date = null)
    {
        $em = $this->getDoctrine()->getManager();

        $requests = $em
            ->getRepository('AppBundle:CarOwnerRequest')
            ->findRequestedByService($carService, null, true, $date);

        $view = \FOS\RestBundle\View\View::create($requests);
        $view->setFormat('json');
        $view->getSerializationContext()->setGroups(array('Default', 'schedule'));

        return $view;
    }


    /**
     * @Method({"POST"})
     * @Route("/car-services/{carServiceId}/requests", name="_schedule_request_create")
     * @ParamConverter("carService", options={"id" = "carServiceId"})
     */
    public function scheduleRequestCreateAction(Request $request, CarService $carService)
    {
        $em = $this->getDoctrine()->getManager();
        $em->beginTransaction();

        /* @var $coRequest \AppBundle\Entity\CarOwnerRequest */
        $coRequest = new CarOwnerRequest();
        $coRequest->setCarService($carService);

        $originalServices = new ArrayCollection();
        foreach ($coRequest->getServices() as $service) {
            $originalServices[] = $service;
        }

        $form = $this->createForm(new CarOwnerRequestScheduleFormType(), $coRequest, array('em' => $em, 'type' => 'schedule_create_request', 'method' => $request->getMethod(), 'csrf_protection' => false));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $schedule = $coRequest->getSchedule();

            if ($schedule && (null === $schedule->getPost())) {
                $scheduleItems = $em->getRepository('AppBundle:CarServiceSchedule')->findCurrentByCarService(
                        $schedule->getCarService(),
                        $schedule->getStartTime(),
                        $schedule->getEndTime());

                $post = $this->get('app.scheduler')->findFreePost($schedule->getCarService(), $scheduleItems);
                $schedule->setPost($post);
            }

            $em->persist($coRequest);
            $em->persist($schedule);
            $em->persist($coRequest->getCar());
            $em->persist($coRequest->getCarOwner());

            if (false === $this->get('security.authorization_checker')->isGranted('schedule', $coRequest)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            foreach ($coRequest->getServices() as $service) {
                $em->persist($service);
            }
            foreach ($originalServices as $service) {
                if (false === $coRequest->getServices()->contains($service)) {
                    $em->remove($service);
                }
            }

            $em->flush();
            $em->commit();

            $view = \FOS\RestBundle\View\View::create($coRequest, 200);
            $view->getSerializationContext()->setGroups(array('Default', 'schedule'));

            return $view;
        } else {
            $em->rollback();
            return \FOS\RestBundle\View\View::create($form, 400);
        }

    }

    /**
     * @Method({"PUT", "PATCH"})
     * @Route("/car-services/{carServiceId}/requests/{carOwnerRequestId}", name="_schedule_request_edit")
     * @ParamConverter("carService", options={"id" = "carServiceId"})
     */
    public function scheduleRequestEditAction(Request $request, CarService $carService, $carOwnerRequestId)
    {
        $em = $this->getDoctrine()->getManager();
        $em->beginTransaction();

        /* @var $coRequest \AppBundle\Entity\CarOwnerRequest */
        $coRequest = $em->getRepository('AppBundle:CarOwnerRequest')->find($carOwnerRequestId);

        $form = $this->createForm(new CarOwnerRequestScheduleFormType(), $coRequest, array('em' => $em, 'type' => 'schedule_edit_request', 'method' => $request->getMethod(), 'csrf_protection' => false));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $schedule = $coRequest->getSchedule();

            if ($schedule && (null === $schedule->getPost())) {
                $scheduleItems = $em->getRepository('AppBundle:CarServiceSchedule')->findCurrentByCarService(
                        $schedule->getCarService(),
                        $schedule->getStartTime(),
                        $schedule->getEndTime());

                $post = $this->get('app.scheduler')->findFreePost($schedule->getCarService(), $scheduleItems);
                $schedule->setPost($post);
            }

            $em->persist($coRequest);
            if ($schedule) {
                $em->persist($schedule);
            }

            if (false === $this->get('security.authorization_checker')->isGranted('schedule', $coRequest)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            foreach ($coRequest->getItems() as $item) {
                $em->persist($item);
            }

            $em->flush();
            $em->commit();

            $view = \FOS\RestBundle\View\View::create($coRequest, 200);
            $view->getSerializationContext()->setGroups(array('Default', 'schedule'));

            return $view;
        } else {
            $em->rollback();
            return \FOS\RestBundle\View\View::create($form, 400);
        }

    }

    /**
     * @Method({"GET"})
     * @Route("/car-services/{carServiceId}/requests/{id}", name="_schedule_get_request")
     */
    public function scheduleRequestGetAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:CarOwnerRequest')->find($id);
    }

}