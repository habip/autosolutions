<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Billing\Payment;
use AppBundle\Doctrine\Paginator;


class PaymentRepository extends EntityRepository
{

    public function findAll($page = 1, $limit = 25)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('p')
            ->from('AppBundleBilling:Payment', 'p');

        $paginator = new Paginator($qb, $page, $limit);
        if (null === $page) {
            $paginator->setPage($paginator->getLastPage());
        }

        return $paginator;
    }

}
