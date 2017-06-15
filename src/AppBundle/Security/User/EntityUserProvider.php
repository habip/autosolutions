<?php

namespace AppBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use libphonenumber\PhoneNumberFormat;

class EntityUserProvider implements UserProviderInterface
{
    private $em;
    private $phoneNumberUtil;

    public function __construct(EntityManager $em, $phoneNumberUtil)
    {
        $this->em = $em;
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    public function loadUserByUsername($username) {
        try {
            $phone = $this->phoneNumberUtil->parse($username, 'RU');
        } catch (NumberParseException $e) {
            $phone = null;
        }

        $qb = $this->em
            ->createQueryBuilder()
            ->select(array('u', 'c', 'o', 'a'))
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.company', 'c')
            ->leftJoin('u.carOwner', 'o')
            ->leftJoin('u.agent', 'a')
            ->where('u.email = :username or u.username = :username')
            ->setParameter('username', $username);

        if (null !== $phone) {
            $qb
                ->orWhere('u.phone = :phone')
                ->setParameter('phone', $this->phoneNumberUtil->format($phone, PhoneNumberFormat::E164));
        }

        $q = $qb->getQuery();

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                    'Unable to find an active User object identified by "%s".',
                    $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                    sprintf(
                            'Instances of "%s" are not supported.',
                            $class
                    )
            );
        }

        return $this->em
            ->createQueryBuilder()
            ->select(array('u', 'c'))
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.company', 'c')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getSingleResult();
    }

    public function supportsClass($class) {
        return User::class === $class
        || is_subclass_of($class, User::class);
    }

}