<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Form;

use Rafrsr\ResourceBundle\Entity\ResourceObject;
use Rafrsr\ResourceBundle\Form\DataTransformer\ResourceTransformer;
use Rafrsr\ResourceBundle\EventListener\UploaderSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FileType
 */
class ResourceType extends AbstractType
{

    /**
     * @var UploaderSubscriber
     */
    private $subscriber;

    /**
     * Constructor
     *
     * @param UploaderSubscriber $subscriber
     */
    public function __construct(UploaderSubscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->subscriber);
        $builder->addViewTransformer(new ResourceTransformer());
        $builder->add('file', 'file');
        $builder->add('delete', 'checkbox', ['required' => false]);
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ResourceObject $resource */
        $resource = $form->getData();

        $view->vars['uniqid'] = 'uploader_' . substr(md5(mt_rand()), 0, 8);
        $view->vars['resource'] = $resource;
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['download'] = $options['download'];
        $view->vars['icon'] = $options['icon'];
        $view->vars['form'] = $view;
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'placeholder' => 'Select a file...',
                    'icon' => 'fa fa-file-o',
                    'download' => true,
                    'type' => 'file',
                    'data_class' => 'Rafrsr\ResourceBundle\Entity\ResourceObject'
                ]
            )
            ->setAllowedTypes('placeholder', ['string']);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
       return 'rafrsr_resource';
    }
}
