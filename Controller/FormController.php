<?php
namespace Builder\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Builder\FormBundle\Manager\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class FormController extends Controller
{

    //developpement en cours
    public function showAction(Request $request, $id = 0, $pageContent){

        $errors = null;
        $errormessage = "";
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $formName = "";
        if($pageContent != null && $pageContent->getContent() != null)
        {
            $formName = strip_tags($pageContent->getContent()->getContent());
        }
        //Load F_FORM => SERVICE getform($formName, $MODE[VIEW/CREATE/EDIT], $current_user)
        $f_form = $em
            ->getRepository('BuilderFormBundle:F_Form')
            ->findOneBy(array('name' => $formName));

        $entityName = "";
        if($f_form != null && $f_form->getEntity())
        {
            $entityName = $f_form->getEntity();
        }


        $formBuilder = $this->get('form.factory')->createBuilder(
            FormType::class,
            null, //Put here new $entity or existing $entity with $id
            array(
                'validation_groups' => array('default', 'event', 'dynamic')
            )
        );

        //Load formulaire with formBuilder
        $formBuilder = FormBuilder::createForm($f_form, $formBuilder, $user, $em);

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        return $this->render('BuilderFormBundle:Form:form.html.twig',
            array(
                'form' => $form->createView(),
                'f_form' => $f_form,
                'errormessage' => $errormessage,
                'request' => $request,
                'errors' => $errors
            )
        );
    }

}