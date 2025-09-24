<?php

namespace Sempre\RAGBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('rag');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('openai_api_key')->defaultNull()->end()
                ->scalarNode('mistral_api_key')->defaultNull()->end()
                ->scalarNode('embedding_provider')->defaultValue('openai')->end()
            ->end();

        return $treeBuilder;
    }
}
