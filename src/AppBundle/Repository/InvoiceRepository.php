<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Billing\OrderedService;
use AppBundle\Entity\Billing\Service;
use AppBundle\Entity\Billing\Price;
use AppBundle\Entity\User;
use AppBundle\Entity\Billing\Invoice;


class InvoiceRepository extends EntityRepository
{

    public function create(OrderedService $service)
    {
        $invoice = new Invoice();
        $invoice
            ->setUser($service->getUser())
            ->setSum($service->getPrice()->getPrice())
            ->setDescription('Оплата ' . $service->getPrice()->getService()->getName())
            ->setOrderedService($service)
            ;

        return $invoice;
    }

}
