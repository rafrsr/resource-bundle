<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource;

use Rafrsr\ResourceBundle\Model\ResourceObjectInterface;

/**
 * ConfigReaderTrait
 */
trait ConfigReaderTrait
{
    /**
     * get custom mapping for resource if have
     *
     * @param array                   $config   array of global resource config
     * @param ResourceObjectInterface $resource resource
     *
     * @return null|array
     */
    protected function getResourceMapping($config, ResourceObjectInterface $resource)
    {
        if ($resource->getMapping() && isset($config['mappings'][$resource->getMapping()])) {
            return $config['mappings'][$resource->getMapping()];
        } elseif ($resource->getMapping()) {
            $message = sprintf('The resource mapping configuration "%s" doest not exist.', $resource->getMapping());
            throw new \RuntimeException($message);
        }

        return null;
    }

    /**
     * Get one setting for given location
     *
     * @param $key
     * @param $location
     * @param $config
     *
     * @return null
     */
    protected function getLocationConfig($key, $location, $config)
    {
        if (in_array($key, ['resolver'])) {
            if (isset($this->getLocation($location, $config)[$key])) {
                return $this->getLocation($location, $config)[$key];
            }
        }

        if (isset($this->getLocation($location, $config)['config'][$key])) {
            return $this->getLocation($location, $config)['config'][$key];
        } else {
            return null;
        }
    }

    /**
     * Get array of settings for give location
     *
     * @param $location
     * @param $config
     *
     * @return mixed
     */
    protected function getLocation($location, $config)
    {
        if (isset($config['locations'][$location])) {
            return $config['locations'][$location];
        }

        throw new \RuntimeException(sprintf('The location "%s" cant not be found.', $location));
    }
}