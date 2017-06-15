<?php
namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');
        $rootNode
            ->children()
                ->arrayNode('path_resolver')
                    ->children()
                        ->scalarNode('web_root')->isRequired()->end()
                        ->arrayNode('oneup_uploader')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('directory')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('gaufrette')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->enumNode('type')
                                        ->defaultValue('notlocal')
                                        ->values(array('local', 'notlocal'))
                                    ->end()
                                    ->scalarNode('path')->isRequired()->end()
                                    ->scalarNode('url')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}