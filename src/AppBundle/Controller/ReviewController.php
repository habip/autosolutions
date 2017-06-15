<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Review;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\Type\ReviewFormType;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Form\Type\ReviewResponseFormType;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\PessimisticLockException;

class ReviewController extends FOSRestController
{

    /**
     *
     * @param Request $request
     *
     * @Route("/car-services/{id}/reviews/", name="_car_service_reviews_list")
     * @Method({"GET"})
     * @QueryParam(name="page", requirements="\d+", default="1")
     */
    public function getCarServiceReviews(Request $request, $id)
    {
        $result = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Review')
            ->findByCarService($id, $request->query->get('page', 1), 10);

        $headers = array();

        if ($result->haveToPaginate()) {
            $headers['Link'] = array(
                sprintf('<%s>; rel="current"', $this->generateUrl('_car_service_reviews_list', array('id' => $id, 'page' => $result->getPage()), true)),
                sprintf('<%s>; rel="last"', $this->generateUrl('_car_service_reviews_list', array('id' => $id, 'page' => $result->getLastPage()), true))
            );
            if ($result->getPage() > 1) {
                $headers['Link'][] = sprintf('<%s>; rel="prev"', $this->generateUrl('_car_service_reviews_list', array('id' => $id, 'page' => $result->getPage()-1), true));
            }
            if ($result->getPage() < $result->getLastPage()) {
                $headers['Link'][] = sprintf('<%s>; rel="next"', $this->generateUrl('_car_service_reviews_list', array('id' => $id, 'page' => $result->getLastPage()+1), true));
            }
        }

        $view = \FOS\RestBundle\View\View::create($result->getResult(), 200, $headers);

        return $view;
    }

    private function processReviewForm(Request $request, Review $review, EntityManager $em)
    {
        $statusCode = $review->getId() ? 204 : 201;

        $form = $this->createForm(new ReviewFormType($em), $review);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($review);

            if (false === $this->get('security.authorization_checker')->isGranted('create', $review)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            $em->flush();
            $em->commit();

            $view = \FOS\RestBundle\View\View::create($review, $statusCode);
            $view->getSerializationContext()->setGroups(array('Default'));

            return $view;
        } else {
            $em->rollback();
            return \FOS\RestBundle\View\View::create($form, 400);
        }
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/requests/{id}/review", name="_create_request_review")
     * @Method({"POST"})
     */
    public function createRequestReview(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->beginTransaction();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        try {
            $carOwnerRequest = $em->find('AppBundle:CarOwnerRequest', $id, LockMode::PESSIMISTIC_WRITE);
        } catch (PessimisticLockException $ex) {
            $em->rollback();
            return \FOS\RestBundle\View\View::create(array('code' => 400, 'message' => 'Ошибка одновременного доступа, попробуйте еще раз позднее'), 400);
        } catch (\Exception $ex) {
            $em->rollback();
            throw new NotFoundHttpException('Заявка не найдена');
        }

        if ($carOwnerRequest->getReview()) {
            $em->rollback();
            return \FOS\RestBundle\View\View::create(array('code' => 400, 'message' => 'Вы уже оставили отзыв на эту заявку'), 400);
        }

        $review = new Review();
        $review
            ->setCarOwnerRequest($carOwnerRequest)
            ->setUser($user);

        return $this->processReviewForm($request, $review, $em);
    }

    private function processReviewResponseForm(Request $request, Review $review, EntityManager $em)
    {
        $statusCode = $review->getId() ? 204 : 201;

        $form = $this->createForm(new ReviewResponseFormType($em), $review);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($review);

            if (false === $this->get('security.authorization_checker')->isGranted('create', $review)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            $em->flush();

            $view = \FOS\RestBundle\View\View::create($review, $statusCode);
            $view->getSerializationContext()->setGroups(array('Default'));

            return $view;
        } else {
            return \FOS\RestBundle\View\View::create($form, 400);
        }
    }

    /**
     *
     * @param Request $request
     * @param integer $id
     *
     * @Route("/car-services/{serviceId}/reviews/{id}/response/", name="_response_to_review")
     * @Method({"POST"})
     */
    public function responseToReview(Request $request, $serviceId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $review = $em->getRepository('AppBundle:Review')->findOneById($id);
        } catch (\Exception $ex) {
            throw new NotFoundHttpException('Отзыв не найден');
        }

        return $this->processReviewResponseForm($request, $review, $em);
    }

}