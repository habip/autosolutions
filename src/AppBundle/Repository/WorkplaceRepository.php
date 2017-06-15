<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Company;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;

class WorkplaceRepository extends EntityRepository
{
    public function findForCompany(Company $company, $recordsPerPage = null, $page = 1, $hydration = Query::HYDRATE_OBJECT)
    {
        $q = $this->_em->createQuery('
                select w
                from AppBundle:Workplace w
                where identity(w.company) = :company');

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
