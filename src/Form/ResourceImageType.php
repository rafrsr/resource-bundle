<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResourceImageType
 */
class ResourceImageType extends ResourceType
{

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['preview'] = $options['preview'];
        $view->vars['preview_class'] = $options['preview_class'];
        $view->vars['preview_max_width'] = $options['preview_max_width'];
        $view->vars['preview_max_height'] = $options['preview_max_height'];
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults(
                [
                    'placeholder' => 'Select a image...',
                    'icon' => 'fa fa-image',
                    'preview' => true,
                    'preview_class' => 'thumbnail',
                    'preview_max_width' => 400,
                    'preview_max_height' => 250,
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'rafrsr_resource_image';
    }
}
