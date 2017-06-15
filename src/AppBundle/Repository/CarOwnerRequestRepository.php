<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use AppBundle\Entity\User;
use AppBundle\Entity\CarOwnerRequest;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Company;
use AppBundle\Entity\CarService;

class CarOwnerRequestRepository extends EntityRepository
{

    public function findByUserForPeriod(User $user, $period, $sortByCar = true)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select(array('r', 'rw', 'i', 'c'))
            ->from('AppBundle:CarOwnerRequest', 'r')
            ->join('r.carOwner', 'o')
            ->leftJoin('r.review', 'rw')
            ->leftJoin('r.items', 'i')
            ->leftJoin('r.car', 'c')
            ->where('identity(o.user) = :user')
            ->setParameter('user', $user->getId());

        if ($sortByCar)
            $qb->orderBy('c.id', 'DESC');
        else
            $qb->orderBy('r.addedTimestamp', 'DESC');

        $periodAdded = false;

        if ($period == 'week') {
            $qb->setParameter('date', new \DateTime('-1 week'));
            $periodAdded = true;
        } else if ($period == 'month') {
            $qb->setParameter('date', new \DateTime('-1 month'));
            $periodAdded = true;
        } else if ($period == 'year') {
            $qb->setParameter('date', new \DateTime('-1 year'));
            $periodAdded = true;
        }

        if ($periodAdded) {
            $qb->andWhere('r.addedTimestamp > :date');
        }

        return $qb->getQuery()->getResult();
    }

    public function findByCompanyForPeriod(Company $company, $period)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select(array('r', 'rw', 'i'))
            ->from('AppBundle:CarOwnerRequest', 'r')
            ->join('r.carService', 'o')
            ->leftJoin('r.review', 'rw')
            ->leftJoin('r.items', 'i')
            ->where('identity(o.company) = :company')
            ->setParameter('company', $company->getId())
            ->orderBy('r.addedTimestamp', 'DESC');

        $periodAdded = false;

        if ($period == 'week') {
            $qb->setParameter('date', new \DateTime('-1 week'));
            $periodAdded = true;
        } else if ($period == 'month') {
            $qb->setParameter('date', new \DateTime('-1 month'));
            $periodAdded = true;
        } else if ($period == 'year') {
            $qb->setParameter('date', new \DateTime('-1 year'));
            $periodAdded = true;
        }

        if ($periodAdded) {
            $qb->andWhere('r.addedTimestamp > :date');
        }

        return $qb->getQuery()->getResult();
    }

    public function findUnreviewed(User $user)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select(array('r', 's', 'c', 'b', 'm'))
            ->from('AppBundle:CarOwnerRequest', 'r')
            ->join('r.carOwner', 'o')
            ->join('r.carService', 's')
            ->join('r.car', 'c')
            ->join('c.brand', 'b')
            ->join('c.model', 'm')
            ->where('identity(o.user) = :user and identity(r.review) is null and r.status = :status')
            ->setParameter('user', $user->getId())
            ->setParameter('status', CarOwnerRequest::STATUS_DONE)
            ->orderBy('r.addedTimestamp', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function restoreFromSession(CarOwnerRequest $request)
    {
        $em = $this->_em;
        $request->setCarService($em->getRepository('AppBundle:CarService')->find($request->getCarService()->getId()));
        $car = $request->getCar();

        $brand = $em->getRepository('AppBundle:Brand')->find($car->getBrand()->getId());
        $model = $em->getRepository('AppBundle:CarModel')->find($car->getModel()->getId());

        if ($car->getId()) {
            $car = $em->getRepository('AppBundle:Car')->findOneBy(array(
                    'id' => $car->getId(),
                    'carOwner' => $request->getCarOwner()
            ));

            $request->setCar($car);
        } else {
            $matchedCars = $em->getRepository('AppBundle:Car')->findBy(array(
                    'model' => $model,
                    'carOwner' => $request->getCarOwner(),
                    'number' => $car->getNumber()
            ));

            if (sizeof($matchedCars) > 0) {
                $request->setCar($matchedCars[0]);
            } else {
                if ($car->getBrand()) {
                    $car->setBrand($brand);
                }
                if ($car->getModel()) {
                    $car->setModel($model);
                }
            }
        }
        $origServices = new ArrayCollection();
        foreach ($request->getServices() as $service) {
            $origServices[] = $service;
        }
        foreach ($origServices as $service) {
            $request->removeService($service);
        }
        foreach ($origServices as $service) {
            $request->addService($em->getRepository('AppBundle:Service')->find($service->getId()));
        }

        $origReasons = new ArrayCollection();
        foreach ($request->getReasons() as $reason) {
            $origReasons[] = $reason;
        }
        foreach ($origReasons as $reason) {
            $request->removeReason($reason);
        }
        foreach ($origReasons as $reason) {
            $request->addReason($em->getRepository('AppBundle:ServiceReason')->find($reason->getId()));
        }
    }

    public function findLastByCompany(Company $company, $count = 5)
    {
        $query = $this->_em->createQuery('
                select r, c, b, m
                from AppBundle:CarOwnerRequest r
                    join r.carService s
                    join r.car c
                    join c.brand b
                    join c.model m
                where identity(s.company) = :company
                order by r.addedTimestamp desc')
            ->setParameter('company', $company->getId());

        $query->setMaxResults($count);

        return $query->getResult();
    }

    public function findScheduledByService(CarService $carService)
    {
        $query = $this->_em->createQuery('
                select r
                from AppBundle:CarOwnerRequest r
                where identity(r.carService) = :carService and r.post is not null
                order by r.entryTime, r.post
                ')
            ->setParameter('carService', $carService->getId());

        return $query->getResult();
    }

    public function findRequestedByService(CarService $carService, $status = null, $showAllNew = false, $startDate = null, $endDate = 'P1D', $timeZone = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select(array('r', 's'))
            ->from('AppBundle:CarOwnerRequest', 'r')
            ->leftJoin('r.schedule', 's')
            ->where('identity(r.carService) = :carService')
            ->orderBy('r.entryTime')
            ->addOrderBy('r.post')
            ->setParameter('carService', $carService->getId())
        ;

        $condition = $qb->expr()->andX();

        if (null !== $status) {
            $condition->add('r.status = :status');
            $qb->setParameter('status', $status);
        }

        if ($startDate === null) {
            $startDate = new \DateTime();
            $startDate->setTime(0, 0, 0);
        }

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
            $e->add(new \DateInterval('P1D'));
        } else if (is_string($endDate)) {
            if (0 === strpos($endDate, 'P')) {
                $e = clone($s);
                $e->add(new \DateInterval($endDate));
            } else {
                $e = $formatter->parse($endDate);
                $e->setTimezone($tz);
            }
        } else if ($endDate instanceof \DateTime) {
            $e = $endDate;
        }

        $formatter->setPattern('yyyy-MM-dd HH:mm:ss');

        $condition->add('s.startTime >= :start and s.startTime < :end');

        $qb
            ->setParameter('start', $formatter->format($s))
            ->setParameter('end', $formatter->format($e))
        ;

        if ($showAllNew) {
            $condition = $qb->expr()->orX($condition, 'r.status = :new');
            $qb->setParameter('new', CarOwnerRequest::STATUS_NEW);
        }

        $qb->andWhere($condition);

        return $qb->getQuery()->getResult();
    }

}