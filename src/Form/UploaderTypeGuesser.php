<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Form;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * Class UploaderTypeGuesser
 */
class UploaderTypeGuesser extends DoctrineOrmTypeGuesser
{

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param ManagerRegistry $registry
     * @param Reader          $reader
     */
    public function __construct(ManagerRegistry $registry, Reader $reader)
    {
        parent::__construct($registry);

        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property)
    {
        if (!$ret = $this->getMetadata($class)) {
            return null;
        }

        /** @var ClassMetadataInfo $metadata */
        list($metadata,) = $ret;
        if ($metadata->isAssociationWithSingleJoinColumn($property)) {
            $reflectionProperty = new \ReflectionProperty($class, $property);

            /** @var \Rafrsr\ResourceBundle\Annotations\ResourceAnnotationInterface $config */
            $interface = 'Rafrsr\ResourceBundle\Annotations\ResourceAnnotationInterface';
            if ($config = $this->reader->getPropertyAnnotation($reflectionProperty, $interface)) {
                return new TypeGuess($config->getFormType(), [], Guess::VERY_HIGH_CONFIDENCE);
            }
        }

        return null;
    }
}
