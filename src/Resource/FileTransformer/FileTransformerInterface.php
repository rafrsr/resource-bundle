<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource\FileTransformer;

use Rafrsr\ResourceBundle\Annotations\ResourceAnnotationInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * FileTransformerInterface
 */
interface FileTransformerInterface
{

    /**
     * Should return true or false if can transform the given file
     *
     * @param File                        $file
     * @param ResourceAnnotationInterface $config
     *
     * @return bool
     */
    public function supports(File $file, ResourceAnnotationInterface $config);

    /**
     * Passing given file this transform the file to resize, compress or any other action
     *
     * @param File                        $file
     * @param ResourceAnnotationInterface $config
     *
     * @return File transformed file
     */
    public function transform(File $file, ResourceAnnotationInterface $config);
}