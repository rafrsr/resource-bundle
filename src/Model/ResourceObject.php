<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Model;

use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ResourceObject
 */
abstract class ResourceObject implements ResourceObjectInterface, \Serializable
{

    /**
     * Name of the mapping to use in order to recover the resource
     *
     * @var string
     *
     * @ORM\Column(name="mapping", type="string", nullable=false)
     */
    protected $mapping;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", nullable=false)
     */
    protected $mimeType = 'application/octet-stream';

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", nullable=false)
     */
    protected $size = 0;

    /**
     * Relative path to use in the mapping url information
     *
     * @var string
     *
     * @ORM\Column(name="relative_path", type="string", nullable=true)
     */
    protected $relativePath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    protected $updated;

    /**
     * Location used to store the file, necessary to recover later
     *
     * @var string
     *
     * @ORM\Column(name="location", type="string", nullable=false)
     */
    protected $location;

    /**
     * File instance
     *
     * @var File
     */
    protected $file;

    /**
     * Url to get the file
     *
     * @var string
     */
    protected $url;

    /**
     * This is not a real field,
     * its only use for internal purposes
     *
     * @var bool
     */
    protected $delete;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @inheritdoc
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * @inheritdoc
     */
    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @inheritdoc
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @inheritdoc
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @inheritdoc
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isDelete()
    {
        return $this->delete;
    }

    /**
     * @inheritdoc
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @inheritdoc
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function serialize()
    {
        $refClass = new \ReflectionClass($this);

        $serialized = [];
        foreach ($refClass->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($this);
            if ($value instanceof File) {
                $value = $value->getRealPath();
            }
            $serialized[$name] = $value;
        }

        return serialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $props = unserialize($serialized);

        $refClass = new \ReflectionClass($this);

        foreach ($props as $prop => $value) {
            if ($refClass->hasProperty($prop)) {
                $property = $refClass->getProperty($prop);
                $property->setAccessible(true);

                if ($property->name == 'file') {
                    $value = new File($value);
                }
                $property->setValue($this, $value);
            }
        }
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl() ?: '';
    }
}