<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\EventListener;

use Rafrsr\ResourceBundle\Model\ResourceObjectInterface;
use Rafrsr\ResourceBundle\Resource\ResourceLoader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class ResourceLoaderListener
 *
 * Note: This listener is injected in the uploaderSubscriber
 */
class ResourceLoaderListener
{
    /**
     * @var object
     */
    private $context;

    /**
     * @var string
     */
    private $property;

    /**
     * @var ResourceLoader
     */
    private $loader;

    /**
     * @var File
     */
    private $file;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * ResourceLoaderListener constructor.
     *
     * @param object         $context
     * @param string         $property
     * @param ResourceLoader $loader
     * @param File           $file
     */
    public function __construct(ResourceLoader $loader, $context, $property, File $file)
    {
        $this->context = $context;
        $this->property = $property;
        $this->loader = $loader;
        $this->file = $file;
        $this->accessor = new PropertyAccessor();
    }

    /**
     * postPersist
     *
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {

        //When the parent is created, the resource is load
        if ($event->getObject() == $this->context) {
            if ($this->accessor->isReadable($this->context, $this->property)
                && $resource = $this->accessor->getValue($this->context, $this->property)
            ) {
                if ($resource instanceof ResourceObjectInterface) {
                    $resource = $this->loader->load($this->context, $this->property, $this->file);

                    //to avoid bubbling with the preUpdate
                    $this->context = null;

                    $event->getEntityManager()->flush($resource);
                }
            }
        } else {
            $meta = $event->getEntityManager()->getClassMetadata(get_class($this->context));
            $identifier = $meta->getSingleIdentifierColumnName();
            //when the parent exists but the resource is created
            if ($meta->getFieldValue($this->context, $identifier)) {
                if ($this->accessor->isReadable($this->context, $this->property)
                    && $event->getObject() == $this->accessor->getValue($this->context, $this->property)
                ) {
                    if (($resource = $event->getObject()) && $resource instanceof ResourceObjectInterface) {
                        $this->loader->load($this->context, $this->property, $this->file);

                        //to avoid bubbling with the preUpdate
                        $this->context = null;

                        $event->getEntityManager()->flush($resource);
                    }
                }
            }
        }

    }

    /**
     * preUpdate
     *
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        if ($this->accessor->isReadable($this->context, $this->property)
            && $event->getObject() == $this->accessor->getValue($this->context, $this->property)
        ) {
            if (($resource = $event->getObject()) && $resource instanceof ResourceObjectInterface) {
                $this->loader->load($this->context, $this->property, $this->file);
            }
        }
    }
}