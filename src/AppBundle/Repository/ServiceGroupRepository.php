<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\CarService;

class ServiceGroupRepository extends EntityRepository
{
    public function findAll()
    {
        return
            $this->_em->createQuery('
                    select g, s, r
                    from AppBundle:ServiceGroup g
                        join g.reason r
                        left join g.services s
                    where s.deletedAt is null
                    order by r.name, g.position, g.name, s.position, s.name
                    ')
                ->getResult();
    }

    public function findForCarService(CarService $carService)
    {
        $groups = array();
        foreach ( $carService->getServiceGroups() as $group ) {
            $groups[] = $group->getId();
        }

        return $this->_em->createQuery('
                    select g, s, r
                    from AppBundle:ServiceGroup g
                        join g.reason r
                        left join g.services s
                    where g.id in (:groups) and s.deletedAt is null
                    order by g.name, s.name
                    ')
                ->setParameter('groups', $groups)
                ->getResult();
    }
}
