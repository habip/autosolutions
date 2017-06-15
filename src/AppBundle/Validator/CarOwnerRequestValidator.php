<?php
namespace AppBundle\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Entity\CarOwnerRequest;

class CarOwnerRequestValidator
{

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function validate($object, ExecutionContextInterface $context)
    {
        if ($object instanceof CarOwnerRequest) {
            if (null === $object->getCarOwnerTimePeriod() && null !== $object->getCarOwnerDate() && $object->getCarService()->isSchedulable()) {
                $requests = $this->em->getRepository('AppBundle:CarOwnerRequest')->findBy(array(
                    'carService' => $object->getCarService(),
                    'car' => $object->getCar(),
                    'carOwnerDate' => $object->getCarOwnerDate(),
                    'carOwnerTimePeriod' => null
                ));

                if (sizeof($requests) > 0 && !(sizeof($requests) == 1 && $requests[0]->getId() == $object->getId())) {
                    $context
                        ->buildViolation('Нельзя записать одну и ту же машину на одно и то же время')
                        ->atPath('carOwnerDate')
                        ->addViolation();
                }
            }
        }
    }

}