<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use AppBundle\Entity\User;

class UserRepository extends EntityRepository
{

    public function findOnline($recordsPerPage = 20, $page = 1, $hydrationMode = Query::HYDRATE_ARRAY)
    {
        $query = $this->_em->createQuery('
                select u
                from AppBundle:User u
                where u.isOnline = true')
                    ->setHydrationMode($hydrationMode);

        if ($recordsPerPage > 0) {
            return new Paginator($query, $page, $hydrationMode);
        } else {
            return $query->getResult();
        }
    }

    public function findCompanies($page = 1, $recordsPerPage = 25)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select(array('u', 'c', 'o'))
            ->from('AppBundle:User', 'u')
            ->join('u.company', 'c')
            ->leftJoin('c.organizationInfo', 'o')
            ->where('u.type = :type')
            ->setParameter('type', User::TYPE_COMPANY);

        $paginator = new Paginator($qb, $page, $recordsPerPage);
        if (null === $page) {
            $paginator->setPage($paginator->getLastPage());
        }

        return $paginator;
    }

}