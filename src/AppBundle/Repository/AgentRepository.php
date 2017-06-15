<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;

class AgentRepository extends EntityRepository
{
    public function findByCarOwner(CarOwner $carOwner)
    {
        return $this->_em->createQuery(
                'select c, b, m
                from AppBundle:Car c
                    left join c.brand b
                    left join c.model m
                where identity(c.carOwner) = :owner')
            ->setParameter('owner', $carOwner->getId())
            ->getResult();
    }
}