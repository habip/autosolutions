<?php
namespace AppBundle\Security\Signer;

class Signer
{
    private $keysPath;
    private $passphrase;

    public function __construct($path, $passphrase)
    {
        $this->keysPath = $path;
        $this->passphrase = $passphrase;
    }

    public function sign($data)
    {
        $prvKeyPath = sprintf('file://%s/private.pem', $this->keysPath);
        $prvKey = openssl_pkey_get_private($prvKeyPath, $this->passphrase);
        $signature = null;
        openssl_sign($data, $signature, $prvKey);

        return $signature;
    }
}