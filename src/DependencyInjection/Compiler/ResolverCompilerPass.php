<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ResolverCompilerPass
 */
class ResolverCompilerPass implements CompilerPassInterface
{

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rafrsr_resource.resolver_manager')) {
            return;
        }

        $definition = $container->getDefinition(
            'rafrsr_resource.resolver_manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'rafrsr_resource.resolver'
        );
        foreach ($taggedServices as $id => $tags) {
            if (!isset($tags[0]['alias'])) {
                throw new \Exception("Error in service '$id', the alias is required for service tagged as rafrsr_resource.resolver");
            }
            $definition->addMethodCall('add', [$tags[0]['alias'], new Reference($id)]);
        }
    }
}