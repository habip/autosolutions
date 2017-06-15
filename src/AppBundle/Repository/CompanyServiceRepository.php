<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\VehicleType;
use AppBundle\Entity\Company;
use AppBundle\Entity\CarService;

class CompanyServiceRepository extends EntityRepository
{
    public function findByCarServiceAndVehicleType(CarService $carService, VehicleType $vehicleType, $query = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('cs', 'cost')
            ->from('AppBundle:CompanyService', 'cs')
            ->join('cs.serviceCosts', 'cost')
            ->join('cs.service', 's')
            ->join('cs.company', 'cm')
            ->join('cm.carServices', 'carSrv')
            ->join('carSrv.services', 'serviceProvided')
            ->where('identity(cs.company) = :company and identity(cost.vehicleType) = :vehicleType and s.id = serviceProvided.id')
            ->setParameter('vehicleType', $vehicleType->getId())
            ->setParameter('company', $carService->getCompany()->getId());

        if ($query) {
            $qb
                ->andWhere('cs.name like :query')
                ->setParameter('query', sprintf('%%%s%%', $query));
        }

        return $qb->getQuery()->getResult();
    }
}
