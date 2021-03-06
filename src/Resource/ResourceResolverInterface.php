<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource;

use Rafrsr\ResourceBundle\Model\ResourceObjectInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ResourceResolverInterface
 */
interface ResourceResolverInterface
{

    /**
     * Set global resource configuration
     *
     * @param array $config
     *
     * @return mixed
     */
    public function setConfig(array $config);

    /**
     * Get file instance for given resource
     *
     * @param ResourceObjectInterface $resource
     *
     * @return File
     */
    public function getFile(ResourceObjectInterface $resource);

    /**
     * Get url to access to the file
     *
     * @param ResourceObjectInterface $resource
     *
     * @return String
     */
    public function getUrl(ResourceObjectInterface $resource);

    /**
     * Save the file
     *
     * @param ResourceObjectInterface $resource resource object containing the tmp file to save
     *
     * @return bool true on success or false on failure.
     */
    public function saveFile(ResourceObjectInterface $resource);

    /**
     * Delete the file related to the resource
     *
     * @param ResourceObjectInterface $resource resource containing the file
     *
     * @return bool true on success or false on failure.
     */
    public function deleteFile(ResourceObjectInterface $resource);
}