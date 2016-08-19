<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource;

use Rafrsr\ResourceBundle\Model\ResourceObjectInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\Session;
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
     * @var Session
     */
    protected $session;

    /**
     * LocalResourceResolver constructor.
     *
     * @param Filesystem $fileSystem
     * @param Session    $session
     */
    public function __construct(Filesystem $fileSystem, Session $session)
    {
        $this->fileSystem = $fileSystem;
        $this->session = $session;
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
     * @inheritdoc
     */
    public function getFile(ResourceObjectInterface $resource)
    {
        $path = $this->getLocationConfig('path', $resource->getLocation(), $this->config);

        if ($resource->getRelativePath()) {
            $path .= $resource->getRelativePath();
        }

        if (is_file($path.DIRECTORY_SEPARATOR.$resource->getName())) {
            return new File($path.DIRECTORY_SEPARATOR.$resource->getName());
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUrl(ResourceObjectInterface $resource)
    {
        $url = $this->getLocationConfig('url', $resource->getLocation(), $this->config);
        preg_match_all('/\{(\w+)\}/', $url, $matches);
        $accessor = new PropertyAccessor();
        if (isset($matches[1])) {
            foreach ($matches[1] as $token) {
                if ($token === 'id') {
                    //when mapping information contains {id}
                    //for security reasons instead of set the real resource id
                    //set a random value and save in session with the real id
                    //the param converter resolve the real resource related for given hash
                    //and keep the resource private for non public access
                    $value = md5(mt_rand());
                    $this->session->set('_resource/'.$value, $resource->getId());
                } else {
                    if ($accessor->isReadable($resource, $token)) {
                        $value = $accessor->getValue($resource, $token);

                    } else {
                        $msg = sprintf('Invalid parameter "{%s}" in %s resource mapping.', $token, $resource->getLocation());
                        throw new \InvalidArgumentException($msg);
                    }
                }
                $url = str_replace("{{$token}}", $value, $url);
            }
        }

        return str_replace('//', '/', $url);
    }

    /**
     * @inheritdoc
     */
    public function saveFile(ResourceObjectInterface $resource)
    {

        $file = $resource->getFile();
        $path = $this->getLocationConfig('path', $resource->getLocation(), $this->config);

        if ($resource->getRelativePath()) {
            $path .= $resource->getRelativePath();
        }

        $this->fileSystem->copy($file->getRealPath(), $path.DIRECTORY_SEPARATOR.$resource->getName());
    }

    /**
     * @inheritdoc
     */
    public function deleteFile(ResourceObjectInterface $resource)
    {
        $path = $this->getLocationConfig('path', $resource->getLocation(), $this->config);

        if ($resource->getRelativePath()) {
            $path .= $resource->getRelativePath();
        }

        $oldFile = $path.DIRECTORY_SEPARATOR.$resource->getName();

        $this->fileSystem->remove($oldFile);

        return !$this->fileSystem->exists($oldFile);
    }
}