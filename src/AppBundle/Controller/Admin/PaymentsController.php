<?php
namespace AppBundle\Controller\Admin;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\CarService;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Billing\Payment;
use AppBundle\Form\Type\PaymentFormType;
use AppBundle\Entity\Billing\Balance;
use Doctrine\DBAL\LockMode;

/**
 *
 * @Route("/admin")
 *
 */
class PaymentsController extends FOSRestController
{
    /**
     *
     * @param Request $request
     *
     * @Route("/payments/", name="_payment_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function paymentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository('AppBundleBilling:Payment')
            ->findAll(
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
     *
     * @param Request $request
     *
     * @Route("/payments/", name="_create_payment")
     * @Method({"POST"})
     */
    public function createPaymentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $payment = new Payment();

        return $this->processPaymentForm($request, $payment, $em);
    }

    private function processPaymentForm(Request $request, Payment $payment, EntityManager $em)
    {
        $statusCode = $payment->getId() ? 204 : 201;

        $form = $this->createForm(new PaymentFormType(), $payment, array('em' => $em, 'method' => $payment->getId() ? 'PUT' : 'POST', 'csrf_protection' => false));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->beginTransaction();

            /* @var $user \AppBundle\Entity\User */
            $user = $em->find('AppBundle:User', $payment->getUser()->getId(), LockMode::PESSIMISTIC_WRITE);

            $balance = new Balance();
            $balance->setIncome($payment->getSum());
            $balance->setPayment($payment);
            $balance->setUser($payment->getUser());

            $this->get('logger')->debug(sprintf('balanace %s before payment persist', $payment->getUser()->getBalance()));
            $em->persist($payment);
            $em->persist($balance);
            $this->get('logger')->debug(sprintf('balanace %s after payment persist', $payment->getUser()->getBalance()));

            $em->flush();

            $em->commit();

            $view = \FOS\RestBundle\View\View::create($payment, $statusCode);
            $view
                ->getSerializationContext()
                ->setGroups(array('Default'));

            return $view;
        }

        $view = \FOS\RestBundle\View\View::create($form, 400);
        return $view;
    }

    /**
     *
     * @param Request $request
     *
     * @Route("/invoices/", name="_invoices_list")
     * @Method({"GET"})
     * @View(serializerGroups={"Default"})
     */
    public function invoicesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $invoiceRepo = $em->getRepository('AppBundleBilling:Invoice');
        $userRepo = $em->getRepository('AppBundle:User');

        if ($request->query->has('filter')
                && ($filters = json_decode($request->query->get('filter'), true))
                && sizeof($filters) > 0 && $filters[0]['property'] == 'user' && $filters[0]['operator'] == '=') {
            $result = $invoiceRepo->findByUser($userRepo->find($filters[0]['value']));

            $view = \FOS\RestBundle\View\View::create(array(
                    'success' => true,
                    'data' => $result,
                    'total' => sizeof($result)
            ));
        } else {
            $result = $invoiceRepo->findAll(
                        $request->query->has('page')?$request->query->get('page'):1,
                        $request->query->has('limit')?$request->query->get('limit'):25);

            $view = \FOS\RestBundle\View\View::create(array(
                    'success' => true,
                    'data' => $result->getResult(),
                    'total' => $result->count()
            ));
        }

        $view
            ->getSerializationContext()
            ->setGroups(array('Default', 'admin'));

        return $view;
    }

}