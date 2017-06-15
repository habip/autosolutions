<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Company;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;

class EmployeeRepository extends EntityRepository
{
    public function findByCompany(Company $company, $recordsPerPage = null, $page = 1, $hydration = Query::HYDRATE_OBJECT)
    {
        $q = $this->_em->createQuery('
                select e, u, c
                from AppBundle:Employee e
                    join e.carService c
                    join e.user u
                where identity(c.company) = :company');

        $q
            ->setParameter('company', $company->getId())
            ->setHydrationMode($hydration);

        if (null !== $recordsPerPage && $recordsPerPage > 0) {
            return new Paginator($q, $page, $recordsPerPage);
        } else {
            return $q->getResult();
        }
    }
}
