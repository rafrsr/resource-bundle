<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class ResourceImage extends ResourceFile
{
    /**
     * @var integer
     */
    public $maxHeight;

    /**
     * @var integer
     */
    public $maxWith;

    /**
     * @var bool
     */
    public $enlarge = false;

    /**
     * @var bool
     */
    public $keepRatio = true;

    /**
     * @inheritDoc
     */
    public function getFormType()
    {
        return 'rafrsr_resource_image';
    }
}
