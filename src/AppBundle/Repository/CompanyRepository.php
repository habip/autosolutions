<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Company;

class CompanyRepository extends EntityRepository
{

    public function getRating(Company $company)
    {
        return $this->_em->createQuery('
                select avg(s.averageRating)
                from AppBundle:CarService s
                where identity(s.company) = :company')
            ->setParameter('company', $company->getId())
            ->getSingleScalarResult();
    }

    public function getServiceReason(Company $company)
    {
        $services = $this->_em->createQuery('
                select s, g, r
                from AppBundle:CarService s
                    join s.serviceGroups g
                    join g.reason r
                where s.company = :company')
            ->setParameter('company', $company->getId())
            ->setMaxResults(1)
            ->getResult();

        if (sizeof($services) > 0 && $services[0]->getServiceGroups()->count() > 0) {
            return $services[0]->getServiceGroups()->get(0)->getReason();
        }

        return null;
    }
}