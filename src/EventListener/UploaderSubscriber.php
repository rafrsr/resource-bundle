<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Rafrsr\ResourceBundle\Annotations\ResourceAnnotationInterface;
use Rafrsr\ResourceBundle\Model\ResourceObjectInterface;
use Rafrsr\ResourceBundle\Resource\ConfigReaderTrait;
use Rafrsr\ResourceBundle\Resource\ResourceLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Imagine\Image\Point;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploaderSubscriber
 */
class UploaderSubscriber implements EventSubscriberInterface
{

    use ConfigReaderTrait;

    /**
     * @var ResourceLoader
     */
    private $loader;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @var array
     */
    private $config;


    /**
     * @param ResourceLoader         $loader
     * @param EntityManagerInterface $em
     * @param AnnotationReader       $reader
     * @param                        $config
     */
    public function __construct(ResourceLoader $loader, EntityManagerInterface $em, AnnotationReader $reader, $config)
    {
        $this->loader = $loader;
        $this->em = $em;
        $this->reader = $reader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        if (null === $config = $this->getPropertyConfig($event)) {
            return;
        }

        /** @var ResourceObjectInterface $resource */
        $resource = $event->getData();
        if ($resource instanceof ResourceObjectInterface) {
            if ($resource && ($file = $resource->getFile()) instanceof UploadedFile) {

                /** @var UploadedFile $file */
                if (!is_file($file->getRealPath())) {
                    $event->getForm()
                        ->addError(new FormError(sprintf('Error uploading file the file %s.', $file->getClientOriginalName())));

                    return;
                }

                $context = $event->getForm()->getParent()->getData();
                $property = $event->getForm()->getName();
                $updater = new ResourceLoaderListener($this->loader, $context, $property, $resource->getFile());

                $resource->setUpdated(new \DateTime()); //force update

                //save initial info, the loader update this information later (postPersist),
                //is required to avoid save empty record
                if (!$resource->getId()) {
                    $resource->setName($file->getClientOriginalName());
                    $resource->setMapping($config->getMapping());

                    if (!$resource->getMapping()) {
                        throw new \RuntimeException('The mapping information is required to upload a resource.');
                    }

                    if (isset($this->config['mappings'][$config->getMapping()]['location'])) {
                        $resource->setLocation($this->config['mappings'][$config->getMapping()]['location']);
                    } else {
                        $resource->setLocation($this->config['default_location']);
                    }
                }

                $this->em->getEventManager()->addEventListener(
                    [
                        Events::postPersist,
                        Events::preUpdate
                    ],
                    $updater
                );
            }
        }
    }

    /**
     * @param FormEvent $event
     *
     * @return ResourceAnnotationInterface|null
     */
    private function getPropertyConfig(FormEvent $event)
    {
        if (null === $class = $event->getForm()->getParent()->getConfig()->getDataClass()) {
            return null;
        }

        $property = $event->getForm()->getName();

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
