<?php
namespace AppBundle\EntityManager;

use AppBundle\Entity\CarOwnerRequest;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\CarServiceSchedule;
use AppBundle\Entity\AppBundle\Entity;
use AppBundle\Entity\CarService;

class CarOwnerRequestScheduler
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function scheduleCarOwnerRequest(CarOwnerRequest $request)
    {
        //if CarOwnerRequest already sheduled
        $schedules = $this->em->getRepository('AppBundle:CarServiceSchdule')->findBy(array(
            'carOwnerRequest' => $request,
            'type' => CarServiceSchedule::TYPE_REQUEST_SCHEDULED
        ));

        //then move it
        if ($schedules) {
            /* @var $schedule \AppBundle\Entity\CarServiceSchedule */
            $schedule = $schedules[0];

            $schedule->setType(CarServiceSchedule::TYPE_MOVED);

            $this->em->persist($schedule);
        }

        //check if time is busy

        $schedule = new CarServiceSchedule();
        $schedule
            ->setCarOwnerRequest($request)
            ->setCarService($request->getCarService())
            ->setPost($request->getPost())
            ->setStartTime($request->getEntryTime())
            ->setEndTime($request->getExitTime())
            ->setType(CarServiceSchedule::TYPE_REQUEST_SCHEDULED);

        $this->em->persist($schedule);
    }

    public function groupByPost($scheduleItems)
    {
        $result = array();

        foreach ($scheduleItems as $schedule) {
            $postId = $schedule->getPost()->getId();
            if (!isset($result[$postId])) {
                $result[$postId] = array();
            }
            $result[$postId][] = $schedule;
        }

        return $result;
    }

    public function findFreePost(CarService $service, $scheduleItems)
    {
        $busyPosts = $this->groupByPost($scheduleItems);
        $freePosts = array();

        foreach ($service->getPosts() as $post) {
            if (!isset($busyPosts[$post->getId()])) {
                $freePosts[] = $post;
            }
        }

        return sizeof($freePosts) > 0? $freePosts[rand(0, sizeof($freePosts) - 1)] : null;
    }

}