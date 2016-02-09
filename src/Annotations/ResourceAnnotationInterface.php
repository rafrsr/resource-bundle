<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Annotations;

/**
 * ResourceAnnotationInterface
 */
interface ResourceAnnotationInterface
{
    /**
     * Name of the mapping configured in the entity
     *
     * @return string
     */
    public function getMapping();

    /**
     * Name of the form to use with this field
     *
     * @return string
     */
    public function getFormType();
}