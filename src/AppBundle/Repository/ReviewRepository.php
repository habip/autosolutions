<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use Doctrine\ORM\EntityRepository;

class ReviewRepository extends EntityRepository
{
    /**
     *
     * @param \AppBundle\Entity\Message\Dialog $dialog
     * @param int $page
     * @param \AppBundle\Entity\User $user
     * @param array $fields
     *
     * @return \AppBundle\Doctrine\Paginator
     */
    public function findByCarService($carServiceId, $page = 1, $recordsPerPage = 20)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select(array('r', 'u', 'c', 'i'))
            ->from('AppBundle:Review', 'r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('u.carOwner', 'c')
            ->leftJoin('u.image', 'i')
            ->leftJoin('i.thumbs', 't', 'with', 't.width = 100 and t.height = 100')
            ->where('identity(r.carService) = :carService')
            ->setParameter('carService', $carServiceId)
            ->orderBy('r.timestamp', 'DESC');

        $paginator = new Paginator($qb, null === $page ? 1 : $page, $recordsPerPage);
        if (null === $page) {
            $paginator->setPage($paginator->getLastPage());
        }

        return $paginator;
    }
}
