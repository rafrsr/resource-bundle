<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Form\DataTransformer;

use Rafrsr\ResourceBundle\Entity\ResourceObject;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ResourceTransformer
 */
class ResourceTransformer implements DataTransformerInterface
{

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if ($value instanceof ResourceObject) {
            if ($value->isDelete() && $value->getFile() == null) {
                $value = null;
            } elseif (!$value->getId() && $value->getFile() == null) {
                $value = null;
            }
        }

        return $value;
    }
}
