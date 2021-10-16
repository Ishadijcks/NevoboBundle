<?php

namespace Punch\NevoboBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('punch_nevobo');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->integerNode('cache_duration')->info('How long to cache results for in seconds. 0 disables the cache')->end()
            ->end();

        return $treeBuilder;
    }
}
