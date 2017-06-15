<?php

namespace AppBundle\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PathResolver
{

    private $container;
    private $config;
    private $gaufretteFileSystems;

    public function __construct(ContainerInterface $container, $config)
    {
        $this->container = $container;

        //create reverse map of gaufrette filesystems
        $this->gaufretteFileSystems = array();
        foreach ($this->container->get('knp_gaufrette.filesystem_map') as $name => $fs) {
            $this->gaufretteFileSystems[spl_object_hash($fs)] = $name;
        }

        $this->config = $config;
    }

    public function getUploadedFileUrl($file, $mapping)
    {
        $path = $file->getRealPath();

        if (isset($this->config['oneup_uploader'][$mapping])) {
            if (strpos($this->config['web_root'], $path) === 0) {
                $baseUrl = $this->config['oneup_uploader'][$mapping]['url'];
                return sprintf('%s%s', $baseUrl, substr($path, strlen($this->config['web_root']) + strlen($baseUrl)));
            } else {
                throw Exception('Cant get url, file not under web root');
            }
        } else {
            throw Exception(sprintf('mapping %s for oneup_uploader not defined', $mapping));
        }
    }

    public function getUploadedFilePath($file)
    {
        $path = $file->getRealPath();

        return $path;
    }

    public function getGaufretteFilePath($file)
    {
        $r = new \ReflectionClass($file);
        $p = $r->getProperty('filesystem');
        $p->setAccessible(true);
        $fs = $p->getValue($file);

        $fsName = $this->gaufretteFileSystems[spl_object_hash($fs)];

        return sprintf('%s/%s', $this->config['gaufrette'][$fsName]['path'], $file->getKey());
    }

    public function getGaufretteFileUrl($file)
    {
        $r = new \ReflectionClass($file);
        $p = $r->getProperty('filesystem');
        $p->setAccessible(true);
        $fs = $p->getValue($file);

        $fsName = $this->gaufretteFileSystems[spl_object_hash($fs)];

        return sprintf('%s/%s', $this->config['gaufrette'][$fsName]['url'], $file->getKey());
    }

    public function getUrl($file, $options = array())
    {
        if (is_a($file, 'Gaufrette\\File')) {
            return $this->getGaufretteFileUrl($file);
        } else if (is_a($file, 'Oneup\\UploaderBundle\\Uploader\\File\\FileInterface') || is_a($file, 'Symfony\\Component\\HttpFoundation\\File\\File')) {
            return $this->getUploadedFileUrl($file, $options['mapping']);
        }
    }

    public function getPath($file)
    {
        if (is_a($file, 'Gaufrette\\File')) {
            return $this->getGaufretteFilePath($file);
        } else if (is_a($file, 'Oneup\\UploaderBundle\\Uploader\\File\\FileInterface') || is_a($file, 'Symfony\\Component\\HttpFoundation\\File\\File')) {
            return $this->getUploadedFilePath($file);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

}