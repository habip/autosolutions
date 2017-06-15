<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ServiceReason;
use AppBundle\Form\Type\ServiceReasonFormType;
use AppBundle\Serializer\Exclusion\IdOnlyExclusionStrategy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ServiceGroup;
use AppBundle\Form\Type\ServiceGroupFormType;
use AppBundle\Entity\Service;
use AppBundle\Form\Type\ServiceFormType;
use AppBundle\Entity\Locality;
use AppBundle\Form\Type\LocalityFormType;
use AppBundle\Entity\District;
use AppBundle\Form\Type\DistrictFormType;
use AppBundle\Entity\MetroStation;
use AppBundle\Form\Type\MetroStationFormType;
use AppBundle\Entity\Highway;
use AppBundle\Form\Type\HighwayFormType;
use AppBundle\Entity\Brand;
use AppBundle\Form\Type\BrandFormType;
use AppBundle\Entity\CarModel;
use AppBundle\Form\Type\CarModelFormType;
use AppBundle\Entity\Dictionary;
use AppBundle\Form\Type\DictionaryFormType;
use AppBundle\Entity\DictionaryItem;
use AppBundle\Form\Type\DictionaryItemFormType;

/**
 *
 * @Route("/admin")
 *
 */
class DictionaryController extends FOSRestController
{

    /**
     *
     * @param Request $request
     *
     * @Route("/service-reasons/", name="_service_reasons_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function serviceReasonsAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:ServiceReason')->findAll();
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/service-reasons/{id}/", name="_service_reason_get")
     * @Method({"GET"})
     */
    public function serviceReasonAction(Request $request, $id)
    {
        $reason = $this->getDoctrine()->getManager()->getRepository('AppBundle:ServiceReason')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($reason);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/service-reasons/{id}", name="_service_reason_edit")
     * @Method({"PUT"})
     */
    public function serviceReasonEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $reason = $em->getRepository('AppBundle:ServiceReason')->findOneById($id);
        return $this->processServiceReasonForm($request, $reason, $em);
    }

    /**
     * @Route("/service-reasons/", name="_service_reason_create")
     * @Method({"POST"})
     */
    public function serviceReasonCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $serviceReason = new ServiceReason();

        return $this->processServiceReasonForm($request, $serviceReason, $em);
    }

    private function processServiceReasonForm(Request $request, ServiceReason $reason, EntityManager $em)
    {
        $statusCode = $reason->getId() ? 204 : 201;

        $originalGroups = new ArrayCollection();

        foreach ($reason->getGroups() as $group) {
            $originalGroups[] = $group;
        }

        $form = $this->createForm(new ServiceReasonFormType($em), $reason, array('method' => $reason->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($reason);

            foreach ($reason->getGroups() as $group) {
                $em->persist($group);
            }

            foreach ($originalGroups as $group) {
                if (false === $reason->getGroups()->contains($group)) {
                    $em->remove($group);
                }
            }

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_service_reason_get', array('id' => $reason->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($reason, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        return \FOS\RestBundle\View\View::create($form, 400);
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/service-groups/", name="_service_groups_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function serviceGroupsAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:ServiceGroup')->findAll();
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/service-groups/{id}/", name="_service_group_get")
     * @Method({"GET"})
     */
    public function serviceGroupAction(Request $request, $id)
    {
        $group = $this->getDoctrine()->getManager()->getRepository('AppBundle:ServiceGroup')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($group);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/service-groups/{id}", name="_service_group_edit")
     * @Method({"PUT"})
     */
    public function serviceGroupEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('AppBundle:ServiceGroup')->findOneById($id);
        return $this->processServiceGroupForm($request, $group, $em);
    }

    /**
     * @Route("/service-groups/", name="_service_group_create")
     * @Method({"POST"})
     */
    public function serviceGroupCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $serviceGroup = new ServiceGroup();

        return $this->processServiceGroupForm($request, $serviceGroup, $em);
    }

    private function processServiceGroupForm(Request $request, ServiceGroup $group, EntityManager $em)
    {
        $statusCode = $group->getId() ? 204 : 201;

        $originalServices = new ArrayCollection();

        foreach ($group->getServices() as $service) {
            $originalServices[] = $service;
        }

        $form = $this->createForm(new ServiceGroupFormType($em), $group, array('method' => $group->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($group);

            foreach ($group->getServices() as $service) {
                $em->persist($service);
            }

            foreach ($originalServices as $service) {
                if (false === $group->getServices()->contains($service)) {
                    $em->remove($service);
                }
            }

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_service_group_get', array('id' => $group->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($group, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/services/", name="_services_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function servicesAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Service')->findBy(array('deletedAt' => null));
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/services/{id}/", name="_service_get")
     * @Method({"GET"})
     */
    public function serviceAction(Request $request, $id)
    {
        $service = $this->getDoctrine()->getManager()->getRepository('AppBundle:Service')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($service);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/services/{id}", name="_service_edit")
     * @Method({"PUT"})
     */
    public function serviceEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $service = $em->getRepository('AppBundle:Service')->findOneById($id);
        return $this->processServiceForm($request, $service, $em);
    }

    /**
     * @Route("/services/", name="_service_create")
     * @Method({"POST"})
     */
    public function serviceCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $service = new Service();

        return $this->processServiceForm($request, $service, $em);
    }

    /**
     * @Route("/services/{id}")
     * @Method({"DELETE"})
     * @param Request $request
     */
    public function serviceRemoveAction(Request $request, Service $service)
    {
        $em = $this->getDoctrine()->getManager();
        $service->setDeletedAt(new \DateTime());
        $em->persist($service);
        $em->flush();
    }

    private function processServiceForm(Request $request, Service $service, EntityManager $em)
    {
        $statusCode = $service->getId() ? 204 : 201;

        $form = $this->createForm(new ServiceFormType($em), $service, array('method' => $service->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($service);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_service_get', array('id' => $service->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($service, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/localities/", name="_localities_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function localitiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->query->has('query')) {
            $result = $em->getRepository('AppBundle:Locality')
                ->findByParameters(
                    array(array('property' => 'name', 'anyMatch' => true, 'value' => $request->query->get('query'))),
                    $request->query->has('page')?$request->query->get('page'):1,
                    $request->query->has('limit')?$request->query->get('limit'):25);
        } else if ($request->query->has('filter')) {
            $result = $em->getRepository('AppBundle:Locality')
                ->findByParameters(
                    json_decode($request->query->get('filter'), true),
                    $request->query->has('page')?$request->query->get('page'):1,
                    $request->query->has('limit')?$request->query->get('limit'):25);
        } else {
            $result = $em->getRepository('AppBundle:Locality')
                ->findAll(
                        $request->query->has('page')?$request->query->get('page'):1,
                        $request->query->has('limit')?$request->query->get('limit'):25);
        }

        $view = \FOS\RestBundle\View\View::create(array(
                'success' => true,
                'data' => $result->getResult(),
                'total' => $result->count()
        ));
        $view
            ->getSerializationContext()
            ->setGroups(array('Default'));

        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{id}/", name="_locality_get")
     * @Method({"GET"})
     */
    public function localityAction(Request $request, $id)
    {
        $locality = $this->getDoctrine()->getManager()->getRepository('AppBundle:Locality')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($locality);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{id}", name="_locality_edit")
     * @Method({"PUT"})
     */
    public function localityEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $locality = $em->getRepository('AppBundle:Locality')->findOneById($id);
        return $this->processLocalityForm($request, $locality, $em);
    }

    /**
     * @Route("/localities/", name="_locality_create")
     * @Method({"POST"})
     */
    public function localityCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $locality = new Locality();

        return $this->processLocalityForm($request, $locality, $em);
    }

    private function processLocalityForm(Request $request, Locality $locality, EntityManager $em)
    {
        $statusCode = $locality->getId() ? 204 : 201;

        $form = $this->createForm(new LocalityFormType($em), $locality, array('method' => $locality->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($locality);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_locality_get', array('id' => $locality->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($locality, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/localities/{id}/districts/", name="_districts_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function districtsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:District')
            ->findByLocality($em->getRepository('AppBundle:Locality')->findOneById($id), array('name' => 'ASC'));

        $view = \FOS\RestBundle\View\View::create(array(
                'success' => true,
                'data' => $result,
                'total' => sizeof($result)
        ));
        $view
            ->getSerializationContext()
            ->setGroups(array('Default'))
            ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{localityId}/districts/{id}", name="_district_get")
     * @Method({"GET"})
     */
    public function districtAction(Request $request, $localityId, $id)
    {
        $locality = $this->getDoctrine()->getManager()->getRepository('AppBundle:District')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($locality);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{localityId}/districts/{id}", name="_district_edit")
     * @Method({"PUT"})
     */
    public function districtEditAction(Request $request, $localityId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $district = $em->getRepository('AppBundle:District')->findOneById($id);
        return $this->processDistrictForm($request, $district, $em);
    }

    /**
     * @Route("/localities/{localityId}/districts/", name="_district_create")
     * @Method({"POST"})
     */
    public function districtCreateAction(Request $request, $localityId)
    {
        $em = $this->getDoctrine()->getManager();

        $district = new District();
        $district->setLocality($em->getRepository('AppBundle:Locality')->findOneById($localityId));
        return $this->processDistrictForm($request, $district, $em);
    }

    private function processDistrictForm(Request $request, District $district, EntityManager $em)
    {
        $statusCode = $district->getId() ? 204 : 201;

        $form = $this->createForm(new DistrictFormType($em), $district, array('method' => $district->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($district);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_district_get', array('localityId' => $district->getLocality()->getId(), 'id' => $district->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($district, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/localities/{id}/metros/", name="_metro_station_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function metroStationsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:MetroStation')
            ->findByLocality($em->getRepository('AppBundle:Locality')->findOneById($id), array('name' => 'ASC'));

        $view = \FOS\RestBundle\View\View::create(array(
                'success' => true,
                'data' => $result,
                'total' => sizeof($result)
        ));
        $view
            ->getSerializationContext()
            ->setGroups(array('Default'))
            ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{localityId}/metros/{id}", name="_metro_station_get")
     * @Method({"GET"})
     */
    public function metroStationAction(Request $request, $localityId, $id)
    {
        $metro = $this->getDoctrine()->getManager()->getRepository('AppBundle:MetroStation')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($metro);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{localityId}/metros/{id}", name="_metro_station_edit")
     * @Method({"PUT"})
     */
    public function metroStationEditAction(Request $request, $localityId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $metro = $em->getRepository('AppBundle:MetroStation')->findOneById($id);
        return $this->processMetroStationForm($request, $metro, $em);
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{localityId}/metros/{id}", name="_metro_station_delete")
     * @Method({"DELETE"})
     */
    public function metroStationDeleteAction(Request $request, $localityId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $metro = $em->getRepository('AppBundle:MetroStation')->findOneById($id);
        try {
            $em->remove($metro);
            $em->flush();

            $view = \FOS\RestBundle\View\View::create(null, 204);
        } catch (\Exception $ex) {
            $view = \FOS\RestBundle\View\View::create(null, 400);
        }
        return $view;
    }

    /**
     * @Route("/localities/{localityId}/metros/", name="_metro_station_create")
     * @Method({"POST"})
     */
    public function metroStationCreateAction(Request $request, $localityId)
    {
        $em = $this->getDoctrine()->getManager();

        $metro = new MetroStation();
        $metro->setLocality($em->getRepository('AppBundle:Locality')->findOneById($localityId));
        return $this->processMetroStationForm($request, $metro, $em);
    }

    private function processMetroStationForm(Request $request, MetroStation $metro, EntityManager $em)
    {
        $statusCode = $metro->getId() ? 204 : 201;

        $form = $this->createForm(new MetroStationFormType($em), $metro, array('method' => $metro->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($metro);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_metro_station_get', array('localityId' => $metro->getLocality()->getId(), 'id' => $metro->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($metro, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/localities/{id}/highways/", name="_highways_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function highwaysAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:Highway')
            ->findByLocality($em->getRepository('AppBundle:Locality')->findOneById($id), array('name' => 'ASC'));

        $view = \FOS\RestBundle\View\View::create(array(
                'success' => true,
                'data' => $result,
                'total' => sizeof($result)
        ));
        $view
            ->getSerializationContext()
            ->setGroups(array('Default'))
            ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{localityId}/highways/{id}", name="_highway_get")
     * @Method({"GET"})
     */
    public function highwayAction(Request $request, $localityId, $id)
    {
        $highway = $this->getDoctrine()->getManager()->getRepository('AppBundle:Highway')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($highway);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/localities/{localityId}/highways/{id}", name="_highway_edit")
     * @Method({"PUT"})
     */
    public function highwayEditAction(Request $request, $localityId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $highway = $em->getRepository('AppBundle:Highway')->findOneById($id);
        return $this->processHighwayForm($request, $highway, $em);
    }

    /**
     * @Route("/localities/{localityId}/highways/", name="_highway_create")
     * @Method({"POST"})
     */
    public function highwayCreateAction(Request $request, $localityId)
    {
        $em = $this->getDoctrine()->getManager();

        $highway = new Highway();
        $highway->setLocality($em->getRepository('AppBundle:Locality')->findOneById($localityId));
        return $this->processHighwayForm($request, $highway, $em);
    }

    private function processHighwayForm(Request $request, Highway $highway, EntityManager $em)
    {
        $statusCode = $highway->getId() ? 204 : 201;

        $form = $this->createForm(new HighwayFormType($em), $highway, array('method' => $highway->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($highway);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_highway_get', array('localityId' => $highway->getLocality()->getId(), 'id' => $highway->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($highway, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/regions/", name="_regions_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function regionsAction(Request $request)
    {
        $result = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Region')
            ->findAll();

        $view = \FOS\RestBundle\View\View::create(array(
                'success' => true,
                'data' => $result,
                'total' => sizeof($result)
        ));
        $view
            ->getSerializationContext()
            ->setGroups(array('Default'))
            ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

        return $view;
    }


    /**
     *
     * @param Request $request
     *
     * @Route("/brands/", name="_brands_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function brandsAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Brand')->findAll();
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/brands/{id}/", name="_brand_get")
     * @Method({"GET"})
     */
    public function brandAction(Request $request, $id)
    {
        $brand = $this->getDoctrine()->getManager()->getRepository('AppBundle:Brand')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($brand);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/brands/{id}", name="_brand_edit")
     * @Method({"PUT"})
     */
    public function brandEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AppBundle:Brand')->findOneById($id);
        return $this->processBrandForm($request, $brand, $em);
    }

    /**
     * @Route("/brands/", name="_brand_create")
     * @Method({"POST"})
     */
    public function brandCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $brand = new Brand();

        return $this->processBrandForm($request, $brand, $em);
    }

    private function processBrandForm(Request $request, Brand $brand, EntityManager $em)
    {
        $statusCode = $brand->getId() ? 204 : 201;

        $form = $this->createForm(new BrandFormType($em), $brand, array('method' => $brand->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($brand);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_brand_get', array('id' => $brand->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($brand, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        return \FOS\RestBundle\View\View::create($form, 400);
    }

    /**
     * @Route("/brands/{id}")
     * @Method({"DELETE"})
     * @param Request $request
     */
    public function brandRemoveAction(Request $request, Brand $brand)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($brand);
        $em->flush();
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/car-models/", name="_car_models_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default", "car_model_brand"})
     */
    public function carModelsAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:CarModel')->findAll();
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/car-models/{id}/", name="_car_model_get")
     * @Method({"GET"})
     */
    public function carModelAction(Request $request, $id)
    {
        $model = $this->getDoctrine()->getManager()->getRepository('AppBundle:CarModel')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($model);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/car-models/{id}", name="_car_model_edit")
     * @Method({"PUT"})
     */
    public function carModelEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $model = $em->getRepository('AppBundle:CarModel')->findOneById($id);
        return $this->processCarModelForm($request, $model, $em);
    }

    /**
     * @Route("/car-models/", name="_car_model_create")
     * @Method({"POST"})
     */
    public function carModelCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $model = new CarModel();

        return $this->processCarModelForm($request, $model, $em);
    }

    private function processCarModelForm(Request $request, CarModel $model, EntityManager $em)
    {
        $statusCode = $model->getId() ? 204 : 201;

        $form = $this->createForm(new CarModelFormType($em), $model, array('method' => $model->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($model);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_car_model_get', array('id' => $model->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($model, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default', 'car_model_brand'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     * @Route("/car-models/{id}")
     * @Method({"DELETE"})
     * @param Request $request
     */
    public function carModelRemoveAction(Request $request, CarModel $carModel)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($carModel);
        $em->flush();
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/legal-forms/", name="_legal_form_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function legalFormsAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:LegalForm')->findAll();
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/dictionaries/", name="_dictionaries_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function dictionariesAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Dictionary')->findAll();
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/dictionaries/{id}/", name="_dictionary_get")
     * @Method({"GET"})
     */
    public function dictionaryAction(Request $request, $id)
    {
        $dictionary = $this->getDoctrine()->getManager()->getRepository('AppBundle:Dictionary')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($dictionary);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/dictionaries/{id}", name="_dictionary_edit")
     * @Method({"PUT"})
     */
    public function dictionaryEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $dictionary = $em->getRepository('AppBundle:Dictionary')->findOneById($id);
        return $this->processDictionaryForm($request, $dictionary, $em);
    }

    /**
     * @Route("/dictionaries/", name="_dictionary_create")
     * @Method({"POST"})
     */
    public function dictionaryCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $dictionary = new Dictionary();

        return $this->processDictionaryForm($request, $dictionary, $em);
    }

    private function processDictionaryForm(Request $request, Dictionary $dictionary, EntityManager $em)
    {
        $statusCode = $dictionary->getId() ? 204 : 201;

        $form = $this->createForm(new DictionaryFormType($em), $dictionary, array('method' => $dictionary->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($dictionary);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_dictionary_get', array('id' => $dictionary->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($dictionary, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        return \FOS\RestBundle\View\View::create($form, 400);
    }


    /**
     *
     * @param Request $request
     *
     * @Route("/dictionaries/{id}/items/", name="_dictionary_items_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function dictionaryItemsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:DictionaryItem')
            ->findByDictionary($em->getRepository('AppBundle:Dictionary')->findOneById($id), array('name' => 'ASC'));

        $view = \FOS\RestBundle\View\View::create(array(
                'success' => true,
                'data' => $result,
                'total' => sizeof($result)
        ));
        $view
            ->getSerializationContext()
            ->setGroups(array('Default'))
            ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/dictionaries/{dictionaryId}/items/{id}", name="_dictionary_item_get")
     * @Method({"GET"})
     */
    public function dictionaryItemAction(Request $request, $dictionaryId, $id)
    {
        $dictionary = $this->getDoctrine()->getManager()->getRepository('AppBundle:DictionaryItem')->findOneById($id);
        $view = \FOS\RestBundle\View\View::create($dictionary);
        return $view;
    }

    /**
     *
     * @param Request $request
     * @param int $id
     *
     * @Route("/dictionaries/{dictionaryId}/items/{id}", name="_dictionary_item_edit")
     * @Method({"PUT"})
     */
    public function dictionaryItemEditAction(Request $request, $dictionaryId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $dictionaryItem = $em->getRepository('AppBundle:DictionaryItem')->findOneById($id);
        return $this->processDictionaryItemForm($request, $dictionaryItem, $em);
    }

    /**
     * @Route("/dictionaries/{dictionaryId}/items/", name="_dictionary_item_create")
     * @Method({"POST"})
     */
    public function dictionaryItemCreateAction(Request $request, $dictionaryId)
    {
        $em = $this->getDoctrine()->getManager();

        $diсtionaryItem = new DictionaryItem();
        $diсtionaryItem->setDictionary($em->getRepository('AppBundle:Dictionary')->findOneById($dictionaryId));
        return $this->processDictionaryItemForm($request, $diсtionaryItem, $em);
    }

    private function processDictionaryItemForm(Request $request, DictionaryItem $dictionaryItem, EntityManager $em)
    {
        $statusCode = $dictionaryItem->getId() ? 204 : 201;

        $form = $this->createForm(new DictionaryItemFormType($em), $dictionaryItem, array('method' => $dictionaryItem->getId() ? 'PUT' : 'POST'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($dictionaryItem);

            $em->flush();

            $headers = array('Location' =>
                    $this->generateUrl(
                            '_dictionary_item_get', array('dictionaryId' => $dictionaryItem->getDictionary()->getId(), 'id' => $dictionaryItem->getId()),
                            true // absolute
                    ));

            $view = \FOS\RestBundle\View\View::create($dictionaryItem, $statusCode, $headers);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'))
                ->addExclusionStrategy(new IdOnlyExclusionStrategy(2));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/vehicle-types/")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function vehicleTypesAction(Request $request)
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:VehicleType')->findAll();
    }

}