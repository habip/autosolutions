<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Doctrine\Paginator;

class LocalityRepository extends EntityRepository
{
    public function findAll($page = 1, $recordsPerPage = 25)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select(array('l'))
            ->from('AppBundle:Locality', 'l')
            ->orderBy('l.name', 'ASC');

        $paginator = new Paginator($qb, $page, $recordsPerPage);
        if (null === $page) {
            $paginator->setPage($paginator->getLastPage());
        }

        return $paginator;
    }

    public function findByParameters($parameters, $page = 1, $recordsPerPage = 25)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select(array('l', 'r'))
            ->from('AppBundle:Locality', 'l')
            ->leftJoin('l.region', 'r')
            ->orderBy('l.name', 'ASC');

        $pCount = 0;

        foreach ($parameters as $parameter) {
            if (isset($this->_class->columnNames[$parameter['property']])) {
                if (isset($parameter['anyMatch']) && $parameter['anyMatch']) {
                    $qb
                        ->andWhere(sprintf('l.%s like :%s', $parameter['property'], 'param' . (++$pCount)))
                        ->setParameter('param' . $pCount, '%' . $parameter['value'] . '%');
                } else if (isset($parameter['operator']) && in_array($parameter['operator'], array('=', '>=', '<=', '>', '<'))) {
                    $qb
                        ->andWhere(sprintf('l.%s %s :%s', $parameter['property'], $parameter['operator'], 'param' . (++$pCount)))
                        ->setParameter('param' . $pCount, $parameter['value']);
                }
            }
        }

        $paginator = new Paginator($qb, $page, $recordsPerPage);
        if (null === $page) {
            $paginator->setPage($paginator->getLastPage());
        }

        return $paginator;
    }
}
