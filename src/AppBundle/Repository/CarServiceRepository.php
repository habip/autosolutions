<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Doctrine\Paginator;

class CarServiceRepository extends EntityRepository
{
    public function findByParams($params = array(), $page = 1, $recordsPerPage = 20, $hydrate = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder('cs')
            ->select('cs, l, i, t')
            ->leftJoin('cs.locality', 'l')
            ->leftJoin('cs.image', 'i')
            ->leftJoin('i.thumbs', 't', 'with', 't.width = :width and t.height = :height and t.isCropped = 1')
            ->setParameter('width', isset($params['width']) && is_numeric($params['width'])? $params['width'] : '272')
            ->setParameter('height', isset($params['height']) && is_numeric($params['height'])? $params['height'] : '181');

        if (isset($params['locality']) && $params['locality']) {
            $qb->andWhere('l.id = :locality')
                ->setParameter('locality', $params['locality']);
        }

        $qb2 = $this->createQueryBuider('cs2')
            ->select('cs2.id');

        $subQuery = false;

        if (isset($params['brand']) && $params['brand']) {
            $qb2->leftJoin('cs.servedCarBrands', 'scb')
                ->andWhere('scb.id = :brand')
                ->setParameter('brand', $params['brand']);

            $subQuery = true;
        }

        if (isset($params['services']) && $params['sevices'] && is_array($params['services'])) {
            $qb2->leftJoin('cs.services', 'sc')
                ->andWhere($qb->expr()->in('sc.id', ':services'))
                ->setParameter('services', $params['services']);

            $subQuery = true;
        }

        if ($subQuery) {
            $qb->andWhere($qb->expr()->in('cs.id', $qb2->getDQL()));
        }

        if ($recordsPerPage > 0) {
            return new Paginator($qb->getQuery()->setHydrationMode($hydrate), $page, $recordsPerPage);
        } else {
            return $qb->getQuery()->getResult($hydrate);
        }
    }

    public function findByUserId($userId)
    {
        return $this->_em->createQuery(
                'select s, l, d, st, h
                from AppBundle:CarService s
                    join s.company c
                    join c.user u
                    left join s.locality l
                    left join s.district d
                    left join s.station st
                    left join s.highway h
                where u.id = :user
                ')
            ->setParameter('user', $userId)
            ->getResult();
    }

    public function findOneById($id, $fields = array())
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('s, l, d, st, h')
            ->from('AppBundle:CarService', 's')
            ->leftJoin('s.locality', 'l')
            ->leftJoin('s.district', 'd')
            ->leftJoin('s.station', 'st')
            ->leftJoin('s.highway', 'h')
            ->where('s.id = :id')
            ->setParameter('id', $id)
        ;

        if (in_array('images', $fields)) {
            $qb
                ->addSelect(array('im', 'izim', 'czim', 'wzim', 'tsim', 'brim', 'boim', 'eim'))
                ->leftJoin('s.image', 'im')
                ->leftJoin('s.inspectorZoneImage', 'izim')
                ->leftJoin('s.clientZoneImage', 'czim')
                ->leftJoin('s.washingZoneImage', 'wzim')
                ->leftJoin('s.tireServiceZoneImage', 'tsim')
                ->leftJoin('s.benchRepairZoneImage', 'brim')
                ->leftJoin('s.bodyRepairZoneImage', 'boim')
                ->leftJoin('s.employeesImage', 'eim')
            ;
        }

        if (in_array('services', $fields)) {
            $qb
                ->addSelect(array('srvs'))
                ->leftJoin('s.services', 'srvs')
            ;
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    function findPopular($locality = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('s, c, l, d, st, h')
            ->from('AppBundle:CarService', 's')
            ->leftJoin('s.company', 'c')
            ->leftJoin('s.locality', 'l')
            ->leftJoin('s.district', 'd')
            ->leftJoin('s.station', 'st')
            ->leftJoin('s.highway', 'h')
            ->orderBy('s.averageRating', 'desc');
            
        if ($locality!=null)
            $qb
                ->where('l.id = :localityId')
                ->setParameter('localityId', $locality->getId());
                
        return $qb->getQuery()->setMaxResults(6)->getResult();
    }
}