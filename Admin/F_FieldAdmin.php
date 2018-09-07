<?php

namespace Builder\FormBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class F_FieldAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->add('property')
            ->add('fieldtype', EntityType::class, array(
                'class' => 'Builder\FormBundle\Entity\F_FieldType',
                'choice_label' => 'name',
                ))
            ->add('listname', 'text', array(
                'label' => 'Liste associée',
                'required' => false
                ))
            ->add('label')
            ->add('placeHolder', 'text', array(
                'label' => 'Place Holder',
                'required' => false
                ))
            ->add('mandatory', CheckboxType::class, array(
                'required' => false
            ))
            ->add('info')
            ->add('limit', 'number', array(
                'label' => 'Taille max',
                'required' => false,
                //'empty_data' => null
            ))
       ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
       $datagridMapper
            ->add('label')
            ->add('property')
       ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
        ->addIdentifier('label', null, array(
            'label' => "Label"
        ))
        ->add('property')
        // ->add('info')
        ->add('fieldtype', null, array(
            'associated_property' => 'name'
        ))
       ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
           ->add('label')
       ;
    }
}