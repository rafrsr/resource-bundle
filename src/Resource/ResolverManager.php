<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource;

/**
 * Class ResolverManager
 */
class ResolverManager
{
    /**
     * @var array
     */
    private $resolvers = [];

    /**
     * Add new resource resolver
     *
     * @param string                    $name
     * @param ResourceResolverInterface $resolver
     */
    public function add($name, ResourceResolverInterface $resolver)
    {
        $this->resolvers[$name] = $resolver;
    }

    /**
     * Get array of all registered
     *
     * @return array|ResourceResolverInterface[]
     */
    public function getAll()
    {
        return $this->resolvers;
    }

    /**
     * Get a resolver by name
     *
     * @param string $name
     *
     * @return ResourceResolverInterface
     * @throws \Exception
     */
    public function get($name)
    {
        if (!isset($this->resolvers[$name])) {
            throw new \Exception("Resource resolver with name '$name' doest not exists.");
        }

        return $this->resolvers[$name];
    }
}