<?php

namespace Builder\FormBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class F_FormAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->add('name', 'text', array(
                'label' => 'Nom'
            ))
            ->add('title', 'text', array(
                'label' => 'Titre du Formulaire'
            ))
            ->add('entity', 'text', array(
                'label' => 'Entité'
            ))
            ->add('formFields', 'sonata_type_collection', array(
                'by_reference' => false,
                'type_options' => array(
                    'delete' => true
                    )
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
            ))
       ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
       $datagridMapper
            ->add('name')
            ->add('entity')
       ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
        ->addIdentifier('name', null, array(
            'label' => "Nom du Formulaire"
        ))
        ->add('entity', null, array(
            'label' => "Entité"
        ))
        ->add('formFields', null, array(
            // 'associated_property' => 'field.label'
            'template' => 'BuilderFormBundle:Admin\List:admin_list_form_formfield.html.twig'
        ))
       ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
           ->add('name')
       ;
    }
}