<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource;

use Rafrsr\ResourceBundle\Annotations\ResourceAnnotationInterface;
use Rafrsr\ResourceBundle\Model\ResourceObjectInterface;
use Rafrsr\ResourceBundle\Resource\FileTransformer\TransformerManager;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Load a resource into a object property
 */
class ResourceLoader
{
    use ConfigReaderTrait;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ResolverManager
     */
    private $resolverManager;

    /**
     * @var TransformerManager
     */
    private $transformerManager;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $class;

    /**
     * Constructor
     *
     * @param Reader             $reader             Annotation Reader
     * @param ResolverManager    $resolverManager    resolver manager to get the correct resource resolver
     * @param TransformerManager $transformerManager transformers to use with resources
     * @param array              $config             array of resource configurations
     */
    public function __construct(Reader $reader, ResolverManager $resolverManager, TransformerManager $transformerManager, array $config)
    {
        $this->reader = $reader;
        $this->resolverManager = $resolverManager;
        $this->transformerManager = $transformerManager;
        $this->config = $config;

        $this->class = $config['class'];
    }

    /**
     * Load a resource into a object property and return instance of resource
     *
     * @param object $context  object to inject the resource
     * @param string $property property to use, should have a valid resource annotation
     * @param File   $file     instance of file to inject
     *
     * @return ResourceObjectInterface the resource object instance
     * @throws \Exception
     */
    public function load(&$context, $property, File $file)
    {
        $accessor = new PropertyAccessor();
        if (($resource = $accessor->getValue($context, $property)) && $resource instanceof ResourceObjectInterface) {
            $resource->setFile($file); //update

            //delete any existent
            if ($resource->getId() && $resource->getMapping() && $resource->getLocation()) {
                $previousResolverName = $this->getLocationConfig('resolver', $resource->getLocation(), $this->config);
                $previousResolver = $this->resolverManager->get($previousResolverName);
                $previousResolver->setConfig($this->config);
                $previousResolver->deleteFile($resource);
            }

        } else {
            /** @var ResourceObjectInterface $resource */
            $resource = new $this->class; //create
            $resource->setFile($file);
        }

        if (null === $config = $this->getPropertyConfig($context, $property)) {

            $message = sprintf(
                'Can not create resource for %s in %s.
             Invalid object or annotation is missing.', $property, $context
            );
            throw new \LogicException($message);
        }

        //new file settings
        $resource->setMapping($config->getMapping());
        $resource->setUpdated(new \DateTime());
        $resource->setSize($file->getSize());
        $resource->setMimeType($file->getMimeType());

        if (isset($this->config['mappings'][$config->getMapping()]['location'])) {
            $resource->setLocation($this->config['mappings'][$config->getMapping()]['location']);
        } else {
            $resource->setLocation($this->config['default_location']);
        }

        $resolverName = $this->getLocationConfig('resolver', $resource->getLocation(), $this->config);
        $resolver = $this->resolverManager->get($resolverName);
        $resolver->setConfig($this->config);

        $resourceMapping = $this->getResourceMapping($this->config, $resource);

        if (isset($resourceMapping['relative_path']) && $relativePath = $resourceMapping['relative_path']) {
            $relativePath = $this->parseName($relativePath, $context, $property);
            $resource->setRelativePath($relativePath);
        }
        foreach ($this->transformerManager->getAll() as $transformer) {
            if ($transformer->supports($resource->getFile(), $config)) {
                $file = $transformer->transform($resource->getFile(), $config);
                $resource->setFile($file);
            }
        }

        $this->setResourceName($resource, $property, $context);

        //save
        $resolver->saveFile($resource);

        $resource->setUrl($resolver->getUrl($resource));

        $accessor->setValue($context, $property, $resource);

        return $resource;
    }

    /**
     * Set the name of resource using the context and resource mapping information
     *
     * @param ResourceObjectInterface $resource
     * @param string                  $baseName default name
     * @param object                  $context  context to resolve tokens
     */
    protected function setResourceName(ResourceObjectInterface $resource, $baseName = null, $context = null)
    {
        if ($baseName) {
            if (strpos($baseName, $resource->getFile()->guessExtension()) === false) {
                $filename = $baseName . '.' . $resource->getFile()->guessExtension();
            } else {
                $filename = $baseName;
            }
        } else {
            if ($resource->getId()) {
                $filename = $resource->getName();
            } else {
                //create unique name
                $filename = sha1(uniqid(mt_rand(), true)) . '.' . $resource->getFile()->guessExtension();
            }
        }

        $mapping = $this->getResourceMapping($this->config, $resource);
        if (isset($mapping['name'])) {
            $filename = $this->parseName($mapping['name'], $context, $baseName);
            $filename .= '.' . $resource->getFile()->guessExtension();
            $resource->setName($filename);
            $filename = basename($filename);
        }
        $resource->setName($filename);
    }

    /**
     * parse name and replace tokens with context properties, ej. logo_{id} --> logo_1
     *
     * @param        $name
     * @param object $context
     * @param object $defaultName
     *
     * @return mixed
     */
    protected function parseName($name, $context, $defaultName = null)
    {
        if ($context) {
            preg_match_all('/\{(\w+)\}/', $name, $matches);
            $accessor = new PropertyAccessor();
            if (isset($matches[1])) {
                foreach ($matches[1] as $token) {
                    if ($accessor->isReadable($context, $token)) {
                        $value = str_replace(' ', '_', $accessor->getValue($context, $token));
                        $name = str_replace("{{$token}}", $value, $name);
                    }
                }
            }

            //render default file name into {}
            $defaultName = str_replace(' ', '_', Inflector::tableize($defaultName));
            $name = str_replace('{}', $defaultName, $name);

            //unique id for each token {*}
            while (strpos($name, '{*}') !== false) {
                $name = preg_replace('/\{\*\}/', substr(sha1(uniqid(mt_rand())), 0, 8), $name, 1);
            }

        }

        return $name;
    }

    /**
     * getPropertyConfig
     *
     * @param $context
     * @param $property
     *
     * @return null|ResourceAnnotationInterface
     */
    private function getPropertyConfig($context, $property)
    {
        if (null === $class = get_class($context)) {
            return null;
        }

        if (!property_exists($class, $property)) {
            return null;
        }

        $reflectionProperty = new \ReflectionProperty($class, $property);
        $propertyAnnotations = $this->reader->getPropertyAnnotations($reflectionProperty);

        foreach ($propertyAnnotations as $propertyAnnotation) {
            if ($propertyAnnotation instanceof ResourceAnnotationInterface) {
                return $propertyAnnotation;
            }
        }

        return null;
    }
}