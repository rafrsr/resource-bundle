<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @param Request $request
     * @param string  $id
     *
     * @return BinaryFileResponse
     */
    public function getAction(Request $request, $id)
    {
        $realId = $request->getSession()->get('_resource/'.$id);
        $class = $this->getParameter('rafrsr_resource.config')['class'];

        $resource = null;
        if ($class && $realId) {
            $resource = $this->getDoctrine()->getRepository($class)->find($realId);
        }

        if (!$resource) {
            throw new NotFoundHttpException;
        }

        // Generate response
        $response = new BinaryFileResponse($resource->getFile()->getRealPath());
        $response->setLastModified($resource->getUpdated());

        return $response;
    }
}
