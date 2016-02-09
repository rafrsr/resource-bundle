<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource\FileTransformer;

/**
 * Class TransformerManager
 */
class TransformerManager
{
    /**
     * @var array
     */
    private $transformers = [];

    /**
     * Add new file transformer
     *
     * @param FileTransformerInterface $transformer
     */
    public function add(FileTransformerInterface $transformer)
    {
        $this->transformers[] = $transformer;
    }

    /**
     * Get array of all registered
     *
     * @return array|FileTransformerInterface[]
     */
    public function getAll()
    {
        return $this->transformers;
    }
}