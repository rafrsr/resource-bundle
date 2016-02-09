<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Resource\FileTransformer;

use Rafrsr\ResourceBundle\Annotations\ResourceAnnotationInterface;
use Rafrsr\ResourceBundle\Annotations\ResourceImage;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ImageTransformer
 */
class ImageTransformer implements FileTransformerInterface
{
    /**
     * @inheritDoc
     */
    public function supports(File $file, ResourceAnnotationInterface $config)
    {
        if ($config instanceof ResourceImage && class_exists('Imagine\Gd\Imagine')) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function transform(File $file, ResourceAnnotationInterface $config)
    {
        /** @var  ResourceImage $config */
        //create copy with image extension instead of .tmp, required to save with Imagine
        $newName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file->getFilename() . '.' . $file->guessExtension();
        if (copy($file->getRealPath(), $newName)) {
            $newFile = new File($newName);

            return $this->imageResize($newFile, $config->maxWith, $config->maxHeight, $config->enlarge, $config->keepRatio);
        } else {
            return $file;
        }
    }

    /**
     * imageResize
     *
     * @param File $file
     * @param int  $maxWidth
     * @param int  $maxHeight
     * @param bool $enlarge
     * @param bool $keepRatio
     *
     * @return ImageInterface|File|static
     */
    protected function imageResize(File $file, $maxWidth, $maxHeight, $enlarge = false, $keepRatio = true)
    {
        if ($maxWidth == 0 && $maxHeight == 0) {
            return $file;
        } elseif ($maxHeight == 0) {
            $maxHeight = $maxWidth;
        } elseif ($maxWidth == 0) {
            $maxWidth = $maxHeight;
        }


        $imagine = new Imagine();
        $resizeImg = $imagine->open($file->getRealPath());

        //get the size of the image you're resizing.
        $origHeight = $resizeImg->getSize()->getHeight();
        $origWidth = $resizeImg->getSize()->getWidth();

        if ($keepRatio) {

            //check for longest side, we'll be seeing that to the max value above
            if ($origHeight > $origWidth) {
                $newWidth = ($maxHeight * $origWidth) / $origHeight;
                $newHeight = $maxHeight;
            } else {
                $newHeight = ($maxWidth * $origHeight) / $origWidth;
                $newWidth = $maxWidth;
            }

        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxHeight;
        }

        //dont enlarge small images
        if (!$enlarge) {
            if (($origHeight > $origWidth && $newHeight > $origHeight) || $newWidth > $origWidth) {
                return $file;
            }
        }

        $size = new Box($newWidth, $newHeight);
        $resizeImg->resize($size)->save($file->getRealPath());

        return $file;
    }
}