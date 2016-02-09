<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource;

use Symfony\Component\Filesystem\Filesystem;
use Rafrsr\ResourceBundle\Entity\ResourceObject;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class LocalResourceResolver
 */
class LocalResourceResolver implements ResourceResolverInterface
{
    use ConfigReaderTrait;

    /**
     * Global resources configuration
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * LocalResourceResolver constructor.
     *
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @inheritdoc
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * getFile
     *
     * @param ResourceObject $resource
     *
     * @return null|File
     */
    public function getFile(ResourceObject $resource)
    {
        $path = $this->getLocationConfig('path', $resource->getLocation(), $this->config);

        if ($resource->getRelativePath()) {
            $path .= $resource->getRelativePath();
        }

        if (is_file($path . DIRECTORY_SEPARATOR . $resource->getName())) {
            return new File($path . DIRECTORY_SEPARATOR . $resource->getName());
        }

        return null;
    }

    /**
     * getUrl
     *
     * @param ResourceObject $resource
     *
     * @return null|string
     */
    public function getUrl(ResourceObject $resource)
    {
        $url = $this->getLocationConfig('url', $resource->getLocation(), $this->config);
        preg_match_all('/\{(\w+)\}/', $url, $matches);
        $accessor = new PropertyAccessor();
        if (isset($matches[1])) {
            foreach ($matches[1] as $token) {
                if ($accessor->isReadable($resource, $token)) {
                    $value = $accessor->getValue($resource, $token);
                    $url = str_replace("{{$token}}", $value, $url);
                }
            }
        }

        return str_replace('//', '/', $url);
    }

    /**
     * @inheritdoc
     */
    public function saveFile(ResourceObject $resource)
    {

        $file = $resource->getFile();
        $path = $this->getLocationConfig('path', $resource->getLocation(), $this->config);

        if ($resource->getRelativePath()) {
            $path .= $resource->getRelativePath();
        }

        $this->fileSystem->copy($file->getRealPath(), $path . DIRECTORY_SEPARATOR . $resource->getName());
    }

    /**
     * deleteFile
     *
     * @param ResourceObject $resource
     *
     * @return bool true on success or false on failure.
     */
    public function deleteFile(ResourceObject $resource)
    {
        $path = $this->getLocationConfig('path', $resource->getLocation(), $this->config);

        if ($resource->getRelativePath()) {
            $path .= $resource->getRelativePath();
        }

        $oldFile = $path . DIRECTORY_SEPARATOR . $resource->getName();

        $this->fileSystem->remove($oldFile);

        return !$this->fileSystem->exists($oldFile);
    }
}