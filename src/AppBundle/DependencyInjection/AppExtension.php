<?php
namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AppExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $webDir = realpath($config['path_resolver']['web_root']);

        $rConfig = array(
            'web_root' => realpath($webDir)
        );

        if (isset($config['path_resolver']['oneup_uploader'])) {
            foreach ($config['path_resolver']['oneup_uploader'] as $mapping => $value) {
                $dir = realpath($config['path_resolver']['oneup_uploader'][$mapping]['directory']);
                if (strpos($dir, $webDir) === 0) {
                    if (!isset($rConfig['oneup_uploader'])) $rConfig['oneup_uploader'] = array();
                    $rConfig['oneup_uploader'][$mapping] = $value;
                    $rConfig['oneup_uploader'][$mapping]['url'] = substr($dir, strlen($webDir));
                }
            }
        }

        if (isset($config['path_resolver']['gaufrette'])) {
            foreach ($config['path_resolver']['gaufrette'] as $fsName => $value) {
                if (!isset($rConfig['gaufrette'])) $rConfig['gaufrette'] = array();
                $rConfig['gaufrette'][$fsName] = $value;
                if ($value['type'] === 'local') {
                    //check if dir in web path
                    $dir = realpath($config['path_resolver']['gaufrette'][$fsName]['path']);
                    if (strpos($dir, $webDir) === 0) {
                        $rConfig['gaufrette'][$fsName] = $value;
                        $rConfig['gaufrette'][$fsName]['path'] = $dir;
                        $rConfig['gaufrette'][$fsName]['url'] = substr($dir, strlen($webDir));
                    }
                } else {
                    $rConfig['gaufrette'][$fsName] = $value;
                }
            }
        }

        $container
            ->register('app.path_resolver', 'AppBundle\\Utils\\PathResolver')
            ->addArgument(new Reference('service_container'))
            ->addArgument($rConfig);
    }

    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['KnpGaufretteBundle']) && isset($bundles['OneupUploaderBundle'])) {
            $gConfigs = $container->getExtensionConfig('knp_gaufrette');
            $oConfigs = $container->getExtensionConfig('oneup_uploader');

            $mConfig = $container->getExtensionConfig($this->getAlias());
            $rConfig = isset($mConfig[0]['path_resolver']) ? $mConfig[0]['path_resolver'] : array();

            if (!isset($rConfig['web_root'])) $rConfig['web_root'] = '%kernel.root_dir%/../web';
            if ($oConfigs[0]['mappings']['image']['storage']['type'] == 'filesystem') {
                if (!isset($rConfig['oneup_uploader'])) $rConfig['oneup_uploader'] = array();
                $rConfig['oneup_uploader']['image'] = array(
                    'directory' => $oConfigs[0]['mappings']['image']['storage']['directory']
                );
            }

            foreach ($gConfigs[0]['adapters'] as $name => $aConfig) {
                $type = array_keys($aConfig);
                if ('local' === $type[0]) {
                    foreach ($gConfigs[0]['filesystems'] as $fsName => $fsConfig) {
                        if ($fsConfig['adapter'] === $name) {
                            // Config for this filesystem may be created manually, ignore in that case
                            if (!(isset($rConfig['gaufrette']) && isset($rConfig['gaufrette'][$fsName]))) {
                                if (!isset($rConfig['gaufrette'])) $rConfig['gaufrette'] = array();
                                $rConfig['gaufrette'][$fsName] = array(
                                    'type' => 'local',
                                    'path' => $aConfig['local']['directory']
                                );
                            }
                            break;
                        }
                    }
                }
            }

            $container->prependExtensionConfig('app', array('path_resolver' => $rConfig));
        }
    }
}