<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\ServiceGroup;
use AppBundle\Entity\VehicleType;
use AppBundle\Entity\Company;
use AppBundle\Entity\Brand;
use AppBundle\Entity\CarModel;
use AppBundle\Entity\Service;

class ServiceRepository extends EntityRepository
{
    private $_serviceCostCache = array();

    public function findAll($hydrate = Query::HYDRATE_OBJECT)
    {
        return
            $this->_em->createQuery('
                    select s, g
                    from AppBundle:Service s left join s.group g
                    order by g.id, s.name
                    ')
                ->getResult($hydrate);
    }

    public function getGroups($services)
    {
        $serviceGroups = array();
        $count = 0;
        foreach ($services as $service) {
            if ($count == 0 || $serviceGroups[$count-1]['group'] != $service->getGroup()) {
                $serviceGroups[$count] = array('group' => $service->getGroup(), 'services' => array(), 'reason' => array('id' => $service->getGroup()->getReason()->getId()));
                $count++;
            }
            $serviceGroups[$count-1]['services'][] = $service;
        }
        if ($count > 1 && $serviceGroups[0]['group'] === null) {
            $others = array_shift($serviceGroups);
            $serviceGroups[$count-1] = $others;
        }
        return $serviceGroups;
    }

    public function findByCompany(Company $company, $hydrate = Query::HYDRATE_ARRAY)
    {
        $resultServices = array();

        $carServices =  $this->_em->createQuery('select c, s
                from AppBundle:CarService c
                    join c.services s
                where identity(c.company) = :company')
            ->setParameter('company', $company->getId())
            ->getResult($hydrate);

        foreach ($carServices as $carService) {
            $services = $hydrate == Query::HYDRATE_ARRAY ? $carService['services'] : $carService->getServices();
            foreach ($services as $service) {
                $id = $hydrate == Query::HYDRATE_ARRAY ? $service['id'] : $service->getId();
                if (!isset($resultServices[$id])) {
                    $resultServices[$id] = $service;
                }
            }
        }

        return array_values($resultServices);
    }

    public function findServicesCostAverage(array $services, $carServiceId, CarModel $model)
    {
        $id = $carServiceId instanceof \AppBundle\Entity\CarService ? $carServiceId->getId() : $carServiceId;

        $costs = $this->_em->createQuery('
                select s service, avg(sc.cost) avgCost, count(sc.id) costCount
                from AppBundle:Service s
                    join s.companyServices cmps
                    join  cmps.serviceCosts sc
                    join cmps.company c
                    join c.carServices cs
                where cs.id = :carService and s.id in (:services)
                    and (identity(sc.vehicleType) = :vehicleType)
                group by s.id
                ')
            ->setParameter('carService', $id)
            ->setParameter('services', $services)
            ->setParameter('vehicleType', $model->getVehicleType()->getId())
            ->getResult(Query::HYDRATE_ARRAY);

        return $costs;
    }

    public function findServicesCostMin($services, $carServiceId, CarModel $model)
    {
        $id = $carServiceId instanceof \AppBundle\Entity\CarService ? $carServiceId->getId() : $carServiceId;
        $searchServices = array();
        $result = array();

        if (!$model->getVehicleType()) {
            return $result;
        }

        foreach ($services as $service) {
            $serviceId = $service instanceof Service ? $service->getId() : $service;
            if (isset($this->_serviceCostsCache[$id][$model->getVehicleType()->getId()][$serviceId])) {
                $result[] = $this->_serviceCostsCache[$id][$model->getVehicleType()->getId()][$serviceId];
            } else {
                $searchServices[] = $serviceId;
            }
        }

        $costs = $this->_em->createQuery('
                select s service, min(sc.cost) minCost, count(sc.id) costCount
                from AppBundle:Service s
                    join s.companyServices cmps
                    join  cmps.serviceCosts sc
                    join cmps.company c
                    join c.carServices cs
                where cs.id = :carService and s.id in (:services)
                    and (identity(sc.vehicleType) = :vehicleType)
                group by s.id
                ')
            ->setParameter('carService', $id)
            ->setParameter('services', $searchServices)
            ->setParameter('vehicleType', $model->getVehicleType()->getId())
            ->getResult(Query::HYDRATE_ARRAY);

        foreach ($costs as $cost) {
            $this->_serviceCostCache[$id][$model->getVehicleType()->getId()][$cost['service']['id']] = $cost;
            $result[] = $cost;
        }

        return $result;
    }

}
