<?php

namespace Builder\FormBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class F_FieldTypeAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->add('name', 'text', array(
                'label' => 'Type de champs'
            ))
            ->add('baseType', 'text', array(
                'label' => 'Base Type'
            ))
            ->add('component', 'text', array(
                'label' => 'Composant associé',
                'required' => false
            ))
       ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
       $datagridMapper
            ->add('name')
       ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
        ->addIdentifier('name', null, array(
            'label' => "Type de champs"
        ))
        ->add('baseType', 'text', array(
            'label' => 'Base Type'
        ))
        ->add('component', 'text', array(
            'label' => 'Composant associé',
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