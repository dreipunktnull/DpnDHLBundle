<?php

namespace Dpn\DHLBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dpn_dhl');

        $rootNode
            ->children()
                ->booleanNode('testmode')->defaultTrue()->end()
                ->scalarNode('user')->isRequired()->end()
                ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('epk')->isRequired()->end()
                ->arrayNode('api')
                    ->prototype('scalar')->end()
                    ->isRequired()
                    ->children()
                        ->scalarNode('user')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('tracking')
                    ->prototype('scalar')->end()
                    ->isRequired()
                    ->children()
                        ->booleanNode('use_sandbox')->defaultTrue()->end()
                        ->scalarNode('user')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
