<?php
namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;

class ChangeRepository extends EntityRepository
{

    public function findChangesForUserSince(User $user, $changeId, $hydration = Query::HYDRATE_ARRAY)
    {
        if (is_numeric($changeId)) {
            $changeNum = $changeId;
        } else {
            $changeNum = $this->_em->createQuery('
                    select c.id
                    from AppBundle:Change c
                    where c.guid = :guid')
                ->setParameter('guid', $changeId)
                ->getSingleScalarResult();
        }

        if ($changeNum) {
            return
                $this->_em->createQuery('
                    select c
                    from AppBundle:Change c join c.subscribers s
                    where c.id > :num and identity(s.user) = :user
                    order by c.id')
                ->setParameter('num', $changeNum)
                ->setParameter('user', $user->getId())
                ->getResult($hydration);
        } else {
            return null;
        }
    }
}