<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Entity\CarService;
use AppBundle\Entity\User;
use AppBundle\Form\Type\CarServiceFormType;
use AppBundle\Form\Type\UserAdminFormType;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @Route("/admin")
 *
 */
class CompanyController extends FOSRestController
{
    /**
     *
     * @param Request $request
     *
     * @Route("/companies/", name="_company_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function companiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository('AppBundle:User')
            ->findCompanies(
                    $request->query->has('page')?$request->query->get('page'):1,
                    $request->query->has('limit')?$request->query->get('limit'):25);

        $view = \FOS\RestBundle\View\View::create(array(
                'success' => true,
                'data' => $result->getResult(),
                'total' => $result->count()
        ));

        $view
            ->getSerializationContext()
            ->setGroups(array('Default', 'admin'));

        return $view;
    }
    
    /**
     * @Route("/companies/{id}")
     * @Method({"PUT"})
     */
    public function editCompanyAction(Request $request, User $user)
    {
        $form = $this->createForm(new UserAdminFormType(), $user, array('method' => 'PUT'));
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $view = \FOS\RestBundle\View\View::create(null, 204);
            return $view;
        }
        
        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/companies/{id}/car-services/", name="_company_car_services_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default", "admin"})
     */
    public function carServicesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository('AppBundle:CarService')
            ->findByUserId($id);

        return array(
            'success' => true,
            'data' => $result,
            'total' => sizeof($result)
        );
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/companies/{userId}/car-services/{id}", name="_company_car_service_get")
     * @Method({"GET"})
     */
    public function carServiceAction(Request $request, $userId, $id)
    {
        $carService = $this->getDoctrine()->getManager()->getRepository('AppBundle:CarService')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($carService);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/companies/{userId}/car-services/", name="_company_car_service_create")
     * @Method({"POST"})
     */
    public function carServiceCreateAction(Request $request, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneById($userId);

        $carService = new CarService();
        $carService->setCompany($user->getCompany());

        return $this->processCarServiceForm($request, $carService, $em);
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/companies/{userId}/car-services/{id}", name="_company_car_service_edit")
     * @Method({"PUT"})
     */
    public function carServiceEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $carService = $em->getRepository('AppBundle:CarService')->findOneById($id);
        return $this->processCarServiceForm($request, $carService, $em);
    }

    private function processCarServiceForm(Request $request, CarService $carService, EntityManager $em)
    {
        $statusCode = $carService->getId() ? 204 : 201;

        $form = $this->createForm(new CarServiceFormType(), $carService, array(
                'method' => $carService->getId() ? 'PUT' : 'POST',
                'csrf_protection' => false,
                'additional_fields' => array('isBlocked'),
                'em' => $em
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($carService);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_company_car_service_get', array(
                                    'userId' => $carService->getCompany()->getUser()->getId(),
                                    'id' => $carService->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($carService, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }


}