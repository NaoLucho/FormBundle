<?php

namespace Builder\FormBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class F_FormFieldRightsAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('rights', 'sonata_type_model', array(
                'class' => 'Application\Sonata\UserBundle\Entity\Group',
                'property' => 'name',
                'label' => 'Liste',
                'multiple' => false,
                'btn_add' => false,
            ))
            ->add('mode', ChoiceType::class, array(
                'choices' => array('View' => 'VIEW', 'Create' => 'CREATE', 'Edit' => 'EDIT'),
                'multiple' => true,
                'expanded' => true,
                'label_attr' => array(
                    'class' => 'checkbox-inline'
                ),
                // 'data' => 'VIEW, EDIT'
                //'empty_data' => 'VIEW'
                //'default' => 'VIEW'
                //'placeholder' => 'VIEW'
            )) //Mode est un property string et le component utilise un tableau: conversion nécessaire
            ->get('mode')->addModelTransformer(new CallbackTransformer(
                function ($modesAsString) {
                    // transform the string back to an array
                    return explode(', ', $modesAsString);
                },function ($modesAsArray) {
                    // transform the array to a string
                    return implode(', ', $modesAsArray);
                }
            ))
            // ->add('mode', 'text', array(
            //     'label' => 'Mode: VIEW, CREATE, EDIT'
            // ))
       ;
    //    var_dump($formMapper->get('mode'));
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
       $datagridMapper
            // ->add('rights', null, array(), 'entity', array(
            //     'class' => 'Application\Sonata\UserBundle\Entity\Group',
            //     'choice_label' => 'name',
            //     'label' => 'Champs',
            // ))
            ->add('mode')
       ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, array(
                'label' => "ID"
            ))
            ->add('mode', null, array(
                'label' => "Mode"
            ))
            // ->add('rights', 'string', array(
            //     'template' => 'BuilderFormBundle:Field:list_formfieldright.html.twig'
            // ))    
        // ->add('list.name', null, array(
        //         'label' => 'Liste'
        //     ))
        //     ->addIdentifier('mode', null, array(
        //         'label' => "Valeur"
        //     ))

       ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
           ->add('id', null, array(
                'label' => 'ID'
            ))
            ->add('mode', null, array(
                'label' => "Mode"
            ))
       ;
    }
}