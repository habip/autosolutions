<?php
namespace AppBundle\Validator;

use AppBundle\Entity\CarServiceSchedule;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CarServicePostAvailableValidator
{

    private $em;
    private $scheduler;

    public function __construct($em, $scheduler)
    {
        $this->em = $em;
        $this->scheduler = $scheduler;
    }

    public function validate($object, ExecutionContextInterface $context)
    {
        if ($object instanceof CarServiceSchedule && $object->getStartTime() && $object->getEndTime()) {
            $scheduleItems = $this->em->getRepository('AppBundle:CarServiceSchedule')->findCurrentByCarService(
                    $object->getCarService(),
                    $object->getStartTime(),
                    $object->getEndTime());

            $scheduleItems = array_filter($scheduleItems, function($item) use ($object) {
                if ($item->getId() == $object->getId() ) {
                    return false;
                }
                return true;
            });

            $busyPosts = $this->scheduler->groupByPost($scheduleItems);

            if (sizeof($busyPosts) < sizeof($object->getCarService()->getPosts())) {
                if ($object->getPost() && isset($busyPosts[$object->getPost()->getId()])) {
                    $context
                        ->buildViolation('Post is busy for given time')
                        ->atPath('post')
                        ->addViolation();
                }
            } else {
                $context
                    ->buildViolation('No posts are available for this time')
                    ->atPath('startTime')
                    ->addViolation();
            }
        }
    }

}