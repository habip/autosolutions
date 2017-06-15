<?php
namespace AppBundle\Validator;

use AppBundle\Entity\CarServiceSchedule;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ScheduleTimeValidator
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function validate($object, ExecutionContextInterface $context)
    {
        if ($object instanceof CarServiceSchedule) {
            $uow = $this->em->getUnitOfWork();
            $changeSet = $uow->getEntityChangeSet($object);
            $oldData = $uow->getOriginalEntityData($object);

            $now = new \DateTime();

            if (!isset($oldData['startTime']) || $oldData['startTime'] != $object->getStartTime()) {
                if ($object->getStartTime()->getTimestamp() < $now->getTimestamp()) {
                    $context
                        ->buildViolation('Time cannot be in the past')
                        ->atPath('startTime')
                        ->addViolation();
                }
            }

            if (!isset($oldData['endTime']) || $oldData['endTime'] != $object->getEndTime()) {
                if ($object->getEndTime()->getTimestamp() < $now->getTimestamp()) {
                    $context
                        ->buildViolation('Time cannot be in the past')
                        ->atPath('endTime')
                        ->addViolation();
                }
            }
        }
    }

}