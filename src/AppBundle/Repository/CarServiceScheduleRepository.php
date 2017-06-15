<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use AppBundle\Entity\User;
use AppBundle\Entity\CarOwnerRequest;
use AppBundle\Entity\Company;
use AppBundle\Entity\CarService;
use AppBundle\Entity\CarServicePost;
use AppBundle\Entity\CarServiceSchedule;

class CarServiceScheduleRepository extends EntityRepository
{

    public function findCurrentByCarService(CarService $carService, $startDate, $endDate = null, $interval = 'P1D', $timeZone = null)
    {
        $tz = new \DateTimeZone($timeZone ? $timeZone : date_default_timezone_get());

        $formatter = new \IntlDateFormatter(
                \Locale::getDefault(),
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::SHORT,
                $tz,
                \IntlDateFormatter::GREGORIAN,
                'yyyy-MM-dd'
                );

        if (is_string($startDate)) {
            $s = new \DateTime(sprintf('@%s UTC', $formatter->parse($startDate)));
            $s->setTimezone($tz);
        } else if ($startDate instanceof \DateTime) {
            $s = $startDate;
        } else {
            throw new \Exception('Start date must be parsable date string or DateTime object');
        }

        if (null === $endDate) {
            $e = clone($s);
            $e->add(new \DateInterval($interval));
        } else if (is_string($endDate)) {
            $e = $formatter->parse($endDate);
            $e->setTimezone($tz);
        } else if ($endDate instanceof \DateTime) {
            $e = $endDate;
        }

        $formatter->setPattern('yyyy-MM-dd HH:mm:ss');

        $query = $this->_em->createQuery('
                select s, r
                from AppBundle:CarServiceSchedule s
                    left join s.carOwnerRequest r
                where identity(s.carService) = :carService and (
                    s.startTime >= :s and s.startTime < :e
                    or s.endTime > :s and s.endTime <= :e
                    or s.startTime <= :s and s.endTime >= :e
                )
                order by s.startTime, s.post
                ')
            ->setParameter('carService', $carService->getId())
            ->setParameter('s', $formatter->format($s))
            ->setParameter('e', $formatter->format($e))
        ;

        return $query->getResult();
    }

    public function findByTimeInResult(\DateTime $date, $postOrService, $result)
    {
        $post = $postOrService instanceof CarServicePost ? $postOrService : null;
        $carService = $postOrService instanceof CarService ? $postOrService : $post->getCarService();

        $s = clone($date);
        $e = clone($date);
        $e->add(new \DateInterval(sprintf('PT%sM', $carService->getTimeInterval())));

        $carServiceSchedules = array_filter($result, function($item) use ($s, $e, $post) {
            /* @var $item \AppBundle\Entity\CarServiceSchedule */
            if ((null === $post || $item->getPost()->getId() == $post->getId())
                    && ($item->getStartTime()->getTimestamp() >= $s->getTimestamp() && $item->getStartTime()->getTimestamp() < $e->getTimestamp()
                            || $item->getEndTime()->getTimestamp() > $s->getTimestamp() && $item->getEndTime()->getTimestamp() <= $e->getTimestamp()
                            || $item->getStartTime()->getTimestamp() <= $s->getTimestamp() && $item->getEndTime()->getTimestamp() >= $e->getTimestamp())) {
                                return true;
                            }
                            return false;
        });
        return array_values($carServiceSchedules);
    }

    public function findOneOrNullByTimeInResult(\DateTime $date, $postOrService, $result)
    {
        $carServiceSchedules = $this->findByTimeInResult($date, $postOrService, $result);

        return empty($carServiceSchedules) ? null : $carServiceSchedules[0];
    }

    public function isTimeBusy(\DateTime $date, CarService $service, $result)
    {
        $scheduleItems = $this->findByTimeInResult($date, $service, $result);

        if (sizeof($service->getPosts()) == sizeof($scheduleItems)) {
            return true;
        }

        return false;
    }
}