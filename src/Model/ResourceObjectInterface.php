<?php

/**
 * Mobile-ERP
 *
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE.md', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Model;

use Symfony\Component\HttpFoundation\File\File;

/**
 * ResourceObjectInterface
 */
interface ResourceObjectInterface
{

    /**
     * Unique identifier for resource
     *
     * @return mixed
     */
    public function getId();

    /**
     * Name of the resource
     *
     * @return string
     */
    public function getName();

    /**
     * Name of the resource
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Resource mapping name
     *
     * @return string
     */
    public function getMapping();

    /**
     * Resource mapping name
     *
     * @param string $mapping
     */
    public function setMapping($mapping);

    /**
     * Relative path where is located the resource
     *
     * @return string
     */
    public function getRelativePath();

    /**
     * Relative path to place the resource
     *
     * @param string $path
     */
    public function setRelativePath($path);

    /**
     * MimeType of the resource
     *
     * @return string
     */
    public function getMimeType();

    /**
     * MimeType of the resource
     *
     * @param string $mimeType
     */
    public function setMimeType($mimeType);

    /**
     * Size of the resource in bytes
     *
     * @return string
     */
    public function getSize();

    /**
     * Size of the resource in bytes
     *
     * @param integer $size
     */
    public function setSize($size);

    /**
     * Last modification date
     *
     * @return string
     */
    public function getUpdated();

    /**
     * Last modification date
     *
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated);

    /**
     * Url to access to the resource
     *
     * @return string
     */
    public function getUrl();

    /**
     * Url to access to the resource
     *
     * @param string $url
     */
    public function setUrl($url);

    /**
     * Location where is placed the resource
     *
     * @return string
     */
    public function getLocation();

    /**
     * Location to place the resource
     *
     * @param string $location
     */
    public function setLocation($location);

    /**
     * The resource is marked for deletion
     *
     * @return boolean
     */
    public function isDelete();

    /**
     * Mark the resource for deletion
     *
     * @param boolean $delete
     *
     * @return $this
     */
    public function setDelete($delete);

    /**
     * Get instance of related file
     *
     * @return File
     */
    public function getFile();

    /**
     * Set related file with the resource
     *
     * @param File|null $file
     *
     * @return $this
     */
    public function setFile($file);
}