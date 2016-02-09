<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rafrsr_resource');

        $rootNode->children()->scalarNode('class')->isRequired()->end();
        $rootNode->children()->scalarNode('default_location')->isRequired()->end();

        /** @var NodeBuilder $locations */
        $locations = $rootNode->children()->arrayNode('locations')->useAttributeAsKey('id')->prototype('array')->children();
        $locations->enumNode('resolver')->values(['local', 'ftp'])->isRequired()->end();

        $config = $locations->arrayNode('config')->children();
        $config->scalarNode('path')->end();
        $config->scalarNode('url')->end();
        $config->end();

        $locations->end();

        /** @var NodeBuilder $mappings */
        $mappings = $rootNode->children()->arrayNode('mappings')->useAttributeAsKey('id')->prototype('array')->children();
        $mappings->scalarNode('name')->end();
        $mappings->scalarNode('relative_path')->end();
        $mappings->scalarNode('location')->end();
        $mappings->end();

        return $treeBuilder;
    }

    /**
     * addMappingsSection
     *
     * @param ArrayNodeDefinition $node
     */
    protected function addMappingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('mappings')
            ->useAttributeAsKey('id')
            ->prototype('array')
            ->children()
            ->scalarNode('location')->end()
            ->scalarNode('name')->end()
            ->scalarNode('relative_path')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    /**
     * addResolversSection
     *
     * @param ArrayNodeDefinition $node
     */
    protected function addLocationsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('locations')
            ->useAttributeAsKey('id')
            ->prototype('array')
            ->children()
            ->scalarNode('resolver')->isRequired()->end()
            ->scalarNode('url')->end()
            ->scalarNode('path')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }
}
