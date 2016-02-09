<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Rafrsr\ResourceBundle\Entity\ResourceObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @Route(path="/resource")
 */
class ResourceController extends Controller
{
    /**
     * Get resource
     *
     * @Route(path="/{resource}/{name}")
     *
     * @param ResourceObject $resource
     * @param string         $name
     *
     * @return BinaryFileResponse
     */
    public function getAction(ResourceObject $resource, $name)
    {
        if ($name != $resource->getName()) {
            throw new NotFoundHttpException;
        }

        // Generate response
        $response = new BinaryFileResponse($resource->getFile()->getRealPath());
        $response->setLastModified($resource->getUpdated());

        return $response;
    }
}
