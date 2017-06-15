<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;

class ServiceReasonRepository extends EntityRepository
{
    public function findAll($groups = false, $services = false, $hydration = Query::HYDRATE_OBJECT)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('r')
            ->from('AppBundle:ServiceReason', 'r')
        ;

        if ($groups) {
            $qb
                ->join('r.groups', 'g')
                ->addSelect('g')
                ->addOrderBy('g.position')
                ->addOrderBy('g.name')
            ;
        }

        if ($groups && $services) {
            $qb
                ->join('g.services', 's')
                ->addSelect('s')
                ->addOrderBy('s.position')
                ->addOrderBy('s.name')
            ;
        }

        return $qb->getQuery()->getResult($hydration);
    }

}
