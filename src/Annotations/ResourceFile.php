<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Rafrsr\ResourceBundle\Form\ResourceType;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class ResourceFile implements ResourceAnnotationInterface
{

    /**
     * @var string
     */
    public $mapping;

    /**
     * @return string
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @inheritDoc
     */
    public function getFormType()
    {
        return ResourceType::class;
    }
}
