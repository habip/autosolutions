<?php
namespace AppBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TokenAuthProvider
{
    private $em;
    private $keyPath;
    private $passphrase;
    private $salt;

    public function __construct($entityManager, $keyPath, $passphrase, $salt)
    {
        $this->em = $entityManager;
        $this->keyPath = $keyPath;
        $this->passphrase = $passphrase;
        $this->salt = $salt;
    }

    public function authenticateRaw($token)
    {
        $prvKeyPath = 'file://' . $this->keyPath . '/private.pem';
        $prvKey = openssl_pkey_get_private($prvKeyPath, $this->passphrase);
        $decrypted = null;
        if (openssl_private_decrypt($token, $decrypted, $prvKey)) {
            $hash = crypt($decrypted, sprintf('$2a$07$%s$', $this->salt));

            $result = $this->em->createQuery(
                    'select u, c
                    from AppBundle:User u join u.company c
                    where c.token = :token')
                ->setParameter('token', $hash)
                ->getResult();

            if (sizeof($result) == 1) {
                return $result[0];
            }
        }
        throw new AuthenticationException('Token invalid');
    }
}