<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\EventListener;

use Rafrsr\ResourceBundle\Model\ResourceObjectInterface;
use Rafrsr\ResourceBundle\Resource\ConfigReaderTrait;
use Rafrsr\ResourceBundle\Resource\ResolverManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Rafrsr\ResourceBundle\Resource\ResourceResolverInterface;

/**
 * Class ResourceORMSubscriber
 */
class ResourceORMSubscriber implements EventSubscriber
{
    use ConfigReaderTrait;

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad => 'postLoad',
            Events::preRemove => 'preRemove',
            Events::postRemove => 'postRemove',
        ];
    }

    /**
     * @var ResolverManager
     */
    private $resolverManager;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $toRemove;

    /**
     * @param ResolverManager $resolver
     * @param array           $config
     */
    public function __construct(ResolverManager $resolver, $config)
    {
        $this->resolverManager = $resolver;
        $this->config = $config;
    }

    /**
     * @param LifecycleEventArgs $event The event.
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof ResourceObjectInterface) {
            /** @var ResourceObjectInterface $resource */
            $resource = $event->getObject();
            if ($resource->getLocation() && $resource->getMapping()) {
                $this->loadResource($resource);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $event The event.
     */
    public function preRemove(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof ResourceObjectInterface) {
            /** @var ResourceObjectInterface $resource */
            $resource = $event->getObject();

            if ($resource->getLocation() && $resource->getMapping()) {
                $this->loadResource($resource);
                $this->pendingToRemove($resource);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $event The event.
     */
    public function postRemove(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof ResourceObjectInterface) {
            /** @var ResourceObjectInterface $resource */
            $resource = $event->getObject();

            if ($resource->getLocation() && $resource->getMapping()) {
                $resolverName = $this->getLocationConfig('resolver', $resource->getLocation(), $this->config);
                $resolver = $this->resolverManager->get($resolverName);
                $resolver->setConfig($this->config);
                $this->loadResource($resource);
                $this->remove($resource, $resolver);
            }
        }
    }

    /**
     * addToRemove
     *
     * @param ResourceObjectInterface $resource
     */
    public function pendingToRemove(ResourceObjectInterface $resource)
    {
        $this->toRemove[md5($resource->getFile()->getFilename())] = $resource;
    }

    /**
     * remove
     *
     * @param ResourceObjectInterface   $resource
     * @param ResourceResolverInterface $resolver
     */
    public function remove(ResourceObjectInterface $resource, ResourceResolverInterface $resolver)
    {
        if (isset($this->toRemove[md5($resource->getFile()->getFilename())])) {
            $resolver->deleteFile($resource);
        }
    }

    /**
     * loadResource
     *
     * @param ResourceObjectInterface $resource
     *
     * @throws \Exception
     */
    protected function loadResource(ResourceObjectInterface $resource)
    {
        $resolverName = $this->getLocationConfig('resolver', $resource->getLocation(), $this->config);
        $resolver = $this->resolverManager->get($resolverName);
        $resolver->setConfig($this->config);
        $resource->setFile($resolver->getFile($resource));
        $resource->setUrl($resolver->getUrl($resource));
    }
}