<?php

namespace Lexiphanic\EncryptedCookieBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lexiphanic_encrypted_cookie');
        $rootNode
            ->children()
                ->arrayNode('cookies')
                    ->prototype('scalar')->end()
                    ->defaultValue([])
                ->end()
                ->arrayNode('encryption')
                    ->children()
                        ->scalarNode('service')->end()
                    ->end()
                ->end()
            ->end()
        ->end();
        return $treeBuilder;
    }
}