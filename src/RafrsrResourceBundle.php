<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle;

use Rafrsr\ResourceBundle\DependencyInjection\Compiler\ResolverCompilerPass;
use Rafrsr\ResourceBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RafrsrResourceBundle
 */
class RafrsrResourceBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ResolverCompilerPass());
        $container->addCompilerPass(new TransformerCompilerPass());
    }
}
