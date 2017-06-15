<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\ServiceGroup;
use AppBundle\Entity\VehicleType;
use AppBundle\Entity\Company;

class ServiceCostRepository extends EntityRepository
{
    public function findByGroupAndVehicleType(ServiceGroup $group, VehicleType $vehicleType, Company $company)
    {
        return $this->_em->createQuery('select sc
                from AppBundle:ServiceCost sc
                    join sc.service s
                where identity(s.group) = :group
                    and identity(sc.vehicleType) = :vehicleType
                    and identity(sc.company) = :company')
            ->setParameter('group', $group->getId())
            ->setParameter('vehicleType', $vehicleType->getId())
            ->setParameter('company', $company->getId())
            ->getResult();
    }
}
