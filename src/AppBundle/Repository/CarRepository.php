<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;
use AppBundle\Entity\CarOwner;
use Doctrine\ORM\QueryBuilder;

class CarRepository extends EntityRepository
{
    private function processFields(QueryBuilder $qb, $fields = array())
    {
        $names = array();

        foreach ($fields as $field) {
            if ('car.images' === $field) {
                if (!isset($names['images'])) {
                    $names['images'] = 'i';

                    $qb
                        ->addSelect('i')
                        ->leftJoin('c.images', 'i');
                }
            }

            if (strpos($field, 'car.images.thumbs') === 0) {
                if (isset($names['images'])) {
                    $qb->addSelect('t');
                } else {
                    $names['images'] = 'i';

                    $qb
                        ->addSelect('i, t')
                        ->leftJoin('c.images', 'i');
                }

                $thumbs = $names['images'] . '.thumbs';

                if (preg_match('/[^\.]+\[(\d+)?(?:x(\d+))?\]/', $field, $matches)) {
                    $condition = '';
                    if ($matches[1]) {
                        $condition .= 't.width = ' . $matches[1];
                    }
                    if ($matches[2]) {
                        $condition .= ($condition ? ' and ' : '') . 't.height = ' . $matches[2];
                    }
                    $qb->leftJoin($thumbs, 't', 'with', $condition);
                } else {
                    $qb->leftJoin($thumbs, 't');
                }
            }

            if ('car.carOwner' === $field) {
                if (!isset($names['carOwner'])) {
                    $qb
                        ->addSelect('o')
                        ->leftJoin('c.carOwner', 'o');

                    $names['carOwner'] = 'o';
                }
            }
        }
    }

    public function findOneById($id, $fields = array(), $carOwner = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('c, b, m')
            ->from('AppBundle:Car', 'c')
            ->leftJoin('c.brand', 'b')
            ->leftJoin('c.model', 'm')
            ->where('c.id = :id and c.isDeleted = false')
            ->setParameter('id', $id)
            ;

        $this->processFields($qb, $fields);

        if ($carOwner) {
            $qb
                ->andWhere('identity(c.carOwner) = :carOwner')
                ->setParameter('carOwner', $carOwner->getId());
        }

        return $qb->getQuery()->getSingleResult();
    }

    public function findByCarOwner(CarOwner $carOwner, $fields = array())
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('c, b, m')
            ->from('AppBundle:Car', 'c')
            ->leftJoin('c.brand', 'b')
            ->leftJoin('c.model', 'm')
            ->where('identity(c.carOwner) = :owner and c.isDeleted = false')
            ->setParameter('owner', $carOwner->getId())
            ;

        $this->processFields($qb, $fields);

        return $qb
            ->getQuery()
            ->getResult();
    }
}