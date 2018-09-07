<?php
namespace Builder\FormBundle\Manager\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;

// use SiteBundle\Entity\Geom;
// use AdminBundle\Form\Type\LocalisationType;

use AdminBundle\Form\Type\FileArrayType;

//UTILS pour builder
class FormBuilder
{

    static function createForm($f_form, $formMapper, $user, $em, $mode = null) //, $entity = null
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        
        //$formMapper->with('Section');
        if ($f_form != null) {

            $metadata = $em->getClassMetadata($f_form->getEntity());
            //dump($metadata);
            $docReader = new AnnotationReader();
            $reflect = new \ReflectionClass($f_form->getEntity());
            //dump($reflect);
            foreach ($f_form->getFormFields() as $ffield) {

                //initialisation pour chaque champ
                $options = [];
                $field = $ffield->getField();
                $noError = true;
                $usebuilder = true;
                $options['attr'] = [];
                $options['label_attr'] = [];
                $options['label_attr']['class'] = '';
                $options['constraints'] = [];
                $type = null;

                //verifier que la property existe:
                if (!$reflect->hasProperty($field->getProperty())) {
                    //dump($f_form->getEntity().' does not have property:'.$field->getProperty());
                    // continue;
                }

                //recupérer les informations relatives à la property
                $docInfos = $docReader->getPropertyAnnotations($reflect->getProperty($field->getProperty()));
                //dump($docInfos[0]);

                //VERIFIER LES DROITS =>desactivé pour le moment
                
                //AJOUT DU LABEL:
                //Mandatory:
                $options['required'] = $ffield->getMandatory();
                if ($ffield->getMandatory()) {
                    //check type du champs:

                    //If endwith ToMany:
                    //Ajout dynamic de contraints (@Assert) dans le group dynamic
                    if (strstr(get_class($docInfos[0]), 'ToMany') === "ToMany") {
                        //dump($ffield);
                        //if($field->getProperty()!="ownersAnim"){
                            //is arrayCollection
                            $options['constraints'] = array(
                                new Constraints\Count(array('min' => 1, 'groups' => array('dynamic'))),
                            );
                        // }
                    } else {
                        //is entity or simple type (int/string etc..)
                        $options['constraints'] = array(
                            new Constraints\NotBlank(array('groups' => array('dynamic'))),
                        );
                    }
                }

                // TYPE OF PROPERY WITH Doctrine Metadata.
                // probleme: fieldMappings concerne les champs type simple
                // et associationMappings concerne les champs object(entity)
                // Seule la propriété reflClass contient tous les champs,
                // > ce qui équivaut à passer par ReflectionClass
                // $prop = $field->getProperty();
                // if($field->getFieldType()->getComponent() == "image")
                // {
                //     $prop = str_replace("File","Name",$prop);
                // }
                // $fieldMetadata = $metadata->fieldMappings[$prop];
                // dump($fieldMetadata);
                // // Catch the type
                // $fieldType = $fieldMetadata['type'];
                // dump($fieldType);

                
                // $strMandatory = '';
                // if($ffield->getMandatory())
                //     $strMandatory = ' <sup>*</sup>';
    
                //DEFINE label:
                $options['label'] = $field->getLabel();
                $options['label_attr'] = array_merge(
                    $options['label_attr'],
                    array(
                        'class' => $options['label_attr']['class'] . ' control-label'
                    )
                );

                if (substr($options['label'], -1) !== ":" && substr($options['label'], -1) !== "?") {
                    $options['label'] = $options['label'] . " :";
                }

                //Add info indication: FField.info override Field.info
                $ffield_info = $ffield->getInfo();
                $field_info = $ffield->getField()->getInfo();
                if (null !== $ffield_info && strlen(trim($ffield_info)) > 0) {
                    $options['attr'] = array_merge($options['attr'], array('help' => $ffield_info));
                    //$options['label'] = $options['label']."<small>".$ffield_info."</small>";
                } elseif (null !== $field_info && strlen(trim($field_info)) > 0) {
                    $options['attr'] = array_merge($options['attr'], array('help' => $field_info));
                    //$options['label'] = $options['label']."<small>".$field_info."</small>";
                }


                $errormessage = "";
                $isList = false;
                //CREER LES CHAMPS SELON LEUR TYPE:
                switch ($field->getFieldType()->getComponent()) {
                    case "text":
                        // $fieldBuilder = $formMapper->add($field->getProperty(), TextType::class);
                        $type = TextType::class;
                        $options['attr'] = array_merge(
                            $options['attr'],
                            array(
                                'class' => 'col-sm-7',
                            //'maxlength' => ($field->getLimit()!==null?$field->getLimit():2000)

                            )
                        );
                        $options['label_attr'] = array_merge(
                            $options['label_attr'],
                            array(
                                'class' => $options['label_attr']['class'] . ' col-sm-5'
                            )
                        );
                        //Constraints:
                        if ($field->getLimit() !== null) {
                            //CLIENT constraint:
                            $options['attr'] = array_merge(
                                $options['attr'],
                                array(
                                    'maxlength' => $field->getLimit(),
                                )
                            );
                            //SERVER contraint
                            $options['constraints'] = array_merge(
                                $options['constraints'],
                                array(
                                    new Constraints\Length(array('max' => $field->getLimit(), 'groups' => array('dynamic'))),
                                )
                            );
                        }
                        break;
                    case "email":
                        $type = EmailType::class;

                        $options['attr'] = array_merge(
                            $options['attr'],
                            array(
                                'class' => 'col-sm-7'
                            )
                        );
                        $options['label_attr'] = array_merge(
                            $options['label_attr'],
                            array(
                                'class' => $options['label_attr']['class'] . ' col-sm-5'
                            )
                        );
                        break;
                    case "year":
                        //$type = 'date';
                        // $options['years'] = [2010,2011];
                        $options['attr'] = array_merge(
                            $options['attr'],
                            array(
                                'class' => 'col-sm-7',
                            //'widget' => 'single_text',
                            //'format'      => 'MMMM-yyyyd',
                            )
                        );
                        $options['label_attr'] = array_merge(
                            $options['label_attr'],
                            array(
                                'class' => $options['label_attr']['class'] . ' col-sm-5'
                            )
                        );
                        break;
                    case "textarea":
                        $type = TextareaType::class;
                        $options['attr'] = array_merge(
                            $options['attr'],
                            array(
                                'parent-div-class' => 'col-xs-12 mt-1',
                                'rows' => 4,
                                'placeholder' => $ffield->getField()->getPlaceHolder(),
                                'contenteditable' => true
                            )
                        );
                        $options['label_attr'] = array_merge(
                            $options['label_attr'],
                            array(
                                'class' => $options['label_attr']['class'] . ' col-xs-12'
                            )
                        );
                        //Constraints:
                        if ($field->getLimit() !== null) {
                            //CLIENT constraint:
                            $options['attr'] = array_merge(
                                $options['attr'],
                                array(
                                    'maxlength' => $field->getLimit(),
                                )
                            );
                            //SERVER contraint
                            $options['constraints'] = array_merge(
                                $options['constraints'],
                                array(
                                    new Constraints\Length(array('max' => $field->getLimit(), 'groups' => array('dynamic'))),
                                )
                            );
                        }
                        break;
                    case "number":
                        $type = null;
                        $options['attr'] = array_merge(
                            $options['attr'],
                            array(
                                'class' => 'col-sm-7',
                                'placeholder' => $ffield->getField()->getPlaceHolder()
                            )
                        );
                        $options['label_attr'] = array_merge(
                            $options['label_attr'],
                            array(
                                'class' => $options['label_attr']['class'] . ' col-sm-5'
                            )
                        );
                        break;
                    case "link":
                        break;
                    case "datetime":
                        $type = DateType::class;
                        $options['widget'] = 'single_text';
                        $options['attr'] = [
                            'class' => 'js-datepicker'
                        ];
                        break;
                    case "TextEditor":
                        $type = CKEditorType::class;
                        break;
                    case "select_simple":
                        $isList = true;
                        $options['placeholder'] = "...";
                        $options['label_attr'] = array_merge(
                            $options['label_attr'],
                            array(
                                'class' => $options['label_attr']['class'] . ' col-sm-5'
                            )
                        );
                        $options['attr'] = array_merge(
                            $options['attr'],
                            array(
                                'class' => 'col-sm-7'
                            )
                        );
                        break;
                    case "radio":
                        $isList = true;
                        $options['expanded'] = true;
                        //$options['placeholder'] = 'N/A';
                        $options['placeholder'] = false;

                        if ($field->getFieldType()->getName() == "RadioList") {
                            $options['attr'] = array_merge(
                                $options['attr'],
                                array(
                                    'childclass' => 'multiple-radio-buttons'
                                )
                            );
                            $options['label_attr'] = array_merge(
                                $options['label_attr'],
                                array(
                                    'class' => $options['label_attr']['class'] . ' col-sm-5'
                                )
                            );
                            $options['attr'] = array_merge(
                                $options['attr'],
                                array(
                                    'class' => 'col-sm-7'
                                )
                            );
                        }
                        if ($field->getFieldType()->getName() == "RadioList_classic") {
                            $options['label_attr'] = array_merge(
                                $options['label_attr'],
                                array(
                                    'class' => $options['label_attr']['class'] . ' col-xs-12'
                                )
                            );
                            $options['attr'] = array_merge(
                                $options['attr'],
                                array(
                                    'class' => 'col-xs-12 mt-1'
                                )
                            );
                        }
                        break;
                    case "select_multiple":
                        $isList = true;
                        $options['multiple'] = true;
                        $options['attr'] = array(
                            'class' => 'multi-select-field ml-2'
                        );
                        break;
                    case "checklist":
                        $isList = true;
                        $options['multiple'] = true;
                        $options['expanded'] = true;
                        $options['label_attr'] = array_merge(
                            $options['label_attr'],
                            array(
                                'class' => $options['label_attr']['class'] . ' col-xs-12 mb-1'
                            )
                        );
                        $options['attr'] = array_merge(
                            $options['attr'],
                            array(
                                'columnsize' => 7
                            )
                        );
                        break;
                    
                    case "inactif":
                        $options['mapped'] = false;
                        $options['label'] = "TODO:" . $options['label'];
                        $type = TextType::class;
                        break;

                    // case "image":
                    // case "file":
                    // case "TableObjectivesType"://SPECIFIC ADD FIELD TableObjectivesType extend CollectionType of form P_ProgramObjectiveType
                    // case "TableNbByYearType"://SPECIFIC ADD FIELD TableNbByYearType extend CollectionType of form P_ProgramNbByYearType
                    // case "LocalisationType":
                    // case "ImageArray":
                    // case "FileArray":
                    // case "Rights":
                        
                    default: 
                        $usebuilder = false; 
                        break;
                    // Text
                    // ERREUR DE TYPE
                    // $noError = false;
                    // $errormessage = $errormessage . "ERREUR: " . $field->getFieldType()->getComponent() . " >notMatch";

                }



                if ($isList) {
                    //TYPE
                    $type = EntityType::class;
                    
                    //DETECT IF LIST IS A G_LIST
                    // Si le champ liste commence par "Liste="+ListeName
                    $listname = $field->getListName();
                    $pos = strpos($listname, "Liste=");

                    $isG_List = false;
                    if ($pos === 0) //FIND at start
                    {   //The field use a G_LIST
                        $listname = substr($listname, $pos + 6); //6 = length("Liste=")
                        $isG_List = true;
                    }

                    if ($isG_List) {
                        //LISTE CREE PAR G_LIST
                        $options['class'] = 'BuilderListBundle:G_ListItem';

                        $options['choice_attr'] = function ($val, $key, $index) {
                            $parent = null;
                            if (null !== $val->getParent()) {
                                $parent = $val->getParent()->getId();
                            }

                            return [
                                'parent' => $parent, 
                                'class' => 'checkbox_input'
                            ];
                        };

                        $options['query_builder'] = (function (EntityRepository $er) use ($listname) {
                            $queryBuilder = $er->createQueryBuilder('listitem');
                            $queryBuilder->select('listitem, COALESCE(IDENTITY(listitem.parent), listitem.id) as HIDDEN columnOrder')
                                ->leftjoin('listitem.list', 'list')
                                ->where('list.name = :listname')
                                ->orderBy('columnOrder', 'ASC')
                                ->addOrderBy('listitem.parent', 'ASC')
                                ->addOrderBy('listitem.order', 'ASC')
                                ->setParameter('listname', $listname);
                            
                            return $queryBuilder;
                            
                            // return $er->createQueryBuilder('listitem')
                            //     ->leftJoin('listitem.list', 'list')
                            //     ->where('list.name = :lname')
                            //     //->orderBy('listitem.name', 'ASC')
                            //     ->setParameter('lname', $listname);
                        });
                        $options['choice_label'] = 'name';
                    } else {
                        //LISTE d'entité:
                        //element d'une entité: Namespace:Entity.choice_label#Filter
                        $listparams = explode("#", $listname);
                        $EntityList = explode(".", $listparams[0]);

                        $options['class'] = $EntityList[0];

                        $options['choice_label'] = 'name'; //Default choice_label
                        if (count($EntityList) > 1) {
                            $options['choice_label'] = $EntityList[1];
                        }
    
                        
                        //IF FILTER EXISTS: après le # Property.attribut=Value exemple: owner.id=currentUser.id
                        if (count($listparams) > 1) {

                            // Ajouter la possibilité d'avoir plusieurs conditions: Property.attribut=Value||Property.attribut=Value
                            // $filters = explode("||", $listparams[1]);
                            // foreach($filters as $filterparams){
                                
                            // }
                            $filterparams = explode("=", $listparams[1]);
                            $filterProperty;
                            $filterPropertyAttribut = null;
                            if ($filterparams[0]) //owner.id
                            {
                                $fpProp = explode(".", $filterparams[0]);

                                $filterProperty = $fpProp[0];//owner
                                if (count($fpProp) > 1) {
                                    $filterPropertyAttribut = $fpProp[1];//id
                                }
                            }
                            if ($filterparams[1]) //VALUE exemple: currentUser.id
                            {
                                //basic value
                                $fvalue = $filterparams[1];
    
                                //CONVERT to booleans:
                                if ($fvalue === 'true') {
                                    $fvalue = true;
                                }
                                if ($fvalue === 'false') {
                                    $fvalue = false;
                                }
    
                                //Specific value
                                if ($filterparams[1] === 'currentUser.id') {
                                    $fvalue = $user->getId();
                                }
    
                                
                                // TODO LATER; faire l'introspection multiniveau des propriétés/attribut typés
                                // $fpvalue = explode(".", $filterparams[1]);
                                // $fpvalueElem = $fvalue[0];
                                // if(count($fvalue)>1)
                                // $filterValueEntity = "owner";
                                // $filterValueEntityProperty = "id";                            
                            }

                            $orderBy = $options['choice_label'];
                            //TEMPORARY SOLUTION:
                            //TOBETTER gérer les multiples conditions todo! with || and &&
                            if($field->getProperty() === 'structureAnim'){
                                $options['query_builder'] = (function (EntityRepository $er) use ($filterProperty, $filterPropertyAttribut, $fvalue, $mode) {
                                    $qb = $er->createQueryBuilder('entity');
                                    $qb = $qb->where('entity.isActive = true');
                                    //owner.id=currentUser.id
                                    if($mode !== 'admin'){
                                        //dump($mode);
                                    //if(isset($user) && ($user->hasGroup($group) || $user->hasGroup($group) )){
                                        $qb = $qb->leftJoin('entity.' . $filterProperty, 'entityJoined'); //owner
                                        $qb = $qb->leftJoin('entity.members', 'members'); //member
                                        $qb = $qb->andwhere(
                                            $qb->expr()->orX(
                                                $qb->expr()->eq('entityJoined.' . $filterPropertyAttribut,':filtervalue'),
                                                $qb->expr()->eq('members.id',':filtervalue')
                                            ));
                                        $qb = $qb->setParameter('filtervalue', $fvalue);
                                    }
                                    $qb = $qb->orderBy('entity.name');
                                    return $qb;
                                });
                            } else {
                                $options['query_builder'] = (function (EntityRepository $er) use ($filterProperty, $filterPropertyAttribut, $fvalue, $orderBy) {
                                    $qb = $er->createQueryBuilder('entity');
                                    if (isset($filterPropertyAttribut)) {
                                        $qb = $qb->leftJoin('entity.' . $filterProperty, 'entityJoined');
                                        $qb = $qb->where('entityJoined.' . $filterPropertyAttribut . ' = :filtervalue');
                                    } else {
                                        $qb = $qb->where('entity.' . $filterProperty . ' = :filtervalue');
                                        $qb = $qb->orderBy('entity.'. $orderBy);
                                    }
                                    $qb = $qb->setParameter('filtervalue', $fvalue);
                                    return $qb;
                                });
                            }
                            
                            
                        }

                    }
                }
                
                //MANAGE SPECIFIC FIELDS:
                
                //Nom de la personne en charge de l'animation de l'observatoire
                //DEFAULT VALUE: utilisateur courant
                
                //DUMP field:
                if ($field->getProperty() === 'propertytodump') {
                    dump($field->getProperty());
                    dump($type);
                    dump($options);
                }
                
                //ADD FIELD TO FORM
                if ($noError) {
                    if ($usebuilder) {

                        $formMapper->add($field->getProperty(), $type, $options);

                    } else {
                        if ($field->getFieldType()->getComponent() === "image") {
                            $type = FileType::class;

                            if ( method_exists($formMapper, 'getData') ) {

                                $entity = $formMapper->getData();

                            } else if ( method_exists( $formMapper, 'getAdmin' ) ) {

                                $entity = $formMapper->getAdmin()->getSubject();

                            } else {
                                $entity = null;
                            }
                                
                            if ( isset($entity) && null !== $entity->getImageMapper()) {
                                $mapper = $entity->getImageMapper();
                                
                                $property = $field->getProperty();
                                
                                if ( array_key_exists( $property, $mapper )) {
                                    $propertyName = $entity->getImageMapper()[$property];
                                } else {
                                    $propertyName = null;
                                }
                                
                                $imageName = $propertyAccessor->getValue($entity, $propertyName);
                                //dump($imageName);
                                if ( isset($imageName) ) {
                                    
                                    $options['constraints'] = array();
                                    $options['required'] = false;
                                    $options['label'] = $options['label'] . ' *';
                                }

                            }

                            $options['attr'] = array_merge(
                                $options['attr'],
                                array(
                                    'class' => 'col-sm-7'
                                )
                            );
                            $options['label_attr'] = array_merge(
                                $options['label_attr'],
                                array(
                                    'class' => $options['label_attr']['class'] . ' col-sm-5'
                                )
                            );
                            //SERVER contraint
                            if ($field->getLimit() !== null) {
                                $options['constraints'] = array_merge(
                                    $options['constraints'],
                                    array(
                                        new Constraints\File(array('maxSize' => $field->getLimit() . 'M', 'groups' => array('dynamic'))),
                                    )
                                );
                            }
                            
                            $formMapper->add($field->getProperty(), $type, $options);

                            
                            $fieldName = $field->getProperty();

                        

                        }
                        if ($field->getFieldType()->getComponent() === "file") {
                            $type = FileType::class;

                            if (method_exists($formMapper, 'getData')) {

                                $entity = $formMapper->getData();

                            } else if (method_exists($formMapper, 'getAdmin')) {

                                $entity = $formMapper->getAdmin()->getSubject();

                            } else {
                                $entity = null;
                            }

                            if (null !== $entity->getImageMapper()) {
                                $mapper = $entity->getImageMapper();

                                $property = $field->getProperty();

                                if (array_key_exists($property, $mapper)) {
                                    $propertyName = $entity->getImageMapper()[$property];
                                } else {
                                    $propertyName = null;
                                }

                                $imageName = $propertyAccessor->getValue($entity, $propertyName);

                                if (isset($imageName)) {

                                    $options['constraints'] = array();
                                    $options['required'] = false;
                                    $options['label'] = $options['label'] . ' *';
                                }

                            }

                            $options['attr'] = array_merge(
                                $options['attr'],
                                array(
                                    'class' => 'col-sm-7'
                                )
                            );
                            $options['label_attr'] = array_merge(
                                $options['label_attr'],
                                array(
                                    'class' => $options['label_attr']['class'] . ' col-sm-5'
                                )
                            );
                            //SERVER contraint
                            if ($field->getLimit() !== null) {
                                $options['constraints'] = array_merge(
                                    $options['constraints'],
                                    array(
                                        new Constraints\File(array('maxSize' => $field->getLimit() . 'M', 'groups' => array('dynamic'))),
                                    )
                                );
                            }

                            $formMapper->add($field->getProperty(), $type, $options);


                            $fieldName = $field->getProperty();



                        }
                        
                        if ($field->getFieldType()->getComponent() === "LocalisationType") {
                            
                            //get type geo (from Geom)
                            //get value geo:
                                //get list of departement
                            $locDepartmentData = $em->getRepository('SiteBundle:Geom')
                                ->createQueryBuilder('geom')
                                ->select('geom.nom', 'geom.id', 'geom.type', 'geom.cp')
                                ->where('geom.type = :type')
                                ->setParameter('type', 'departement')
                            //->setMaxResults(10)
                                ->addOrderBy('geom.cp')
                                ->addOrderBy('geom.nom')
                                ->getQuery()->getResult();
                            //var_dump($locDepartmentData);
                            $locDepartmentArray = [];
                            foreach ($locDepartmentData as $loc) {
                                $locDepartmentArray[$loc["id"]] = $loc["cp"] . ' - ' . $loc["nom"];
                            }
                                //get list of region
                            $locRegionData = $em->getRepository('SiteBundle:Geom')
                                ->createQueryBuilder('geom')
                                ->select('geom.nom', 'geom.id', 'geom.type')
                                ->where('geom.type = :type')
                                ->setParameter('type', 'region')
                                ->orderBy('geom.nom')
                            //->setMaxResults(10)
                                ->getQuery()->getResult();
                            //var_dump($locRegionData);
                            $locRegionArray = [];
                            foreach ($locRegionData as $loc) {
                                $locRegionArray["" . $loc["id"]] = $loc["nom"];
                            }

                            $locNationalData = $em->getRepository('SiteBundle:Geom')
                                ->createQueryBuilder('geom')
                                ->select('geom.nom', 'geom.id', 'geom.type')
                                ->where('geom.type = :type')
                                ->setParameter('type', 'national')
                                ->orderBy('geom.nom')
                            //->setMaxResults(10)
                                ->getQuery()->getResult();
                            //var_dump($locRegionData);
                            $locNationalArray = [];
                            foreach ($locNationalData as $loc) {
                                $locNationalArray["" . $loc["id"]] = $loc["nom"];
                            }

                            $locDOMData = $em->getRepository('SiteBundle:Geom')
                                ->createQueryBuilder('geom')
                                ->select('geom.nom', 'geom.id', 'geom.type')
                                ->where('geom.type = :type')
                                ->setParameter('type', 'dom')
                                ->orderBy('geom.nom')
                            //->setMaxResults(10)
                                ->getQuery()->getResult();
                            //var_dump($locRegionData);
                            $locDOMArray = [];
                            foreach ($locDOMData as $loc) {
                                $locDOMArray["" . $loc["id"]] = $loc["nom"];
                            }


                                //get list of zone (littorales et maritimes)
                            $locZoneLMData = $em->getRepository('SiteBundle:Geom')
                                ->createQueryBuilder('geom')
                                ->select('geom.nom', 'geom.id', 'geom.type')
                                ->where('geom.type = :type')
                                ->setParameter('type', 'ocean')
                            //->setMaxResults(10)
                                ->getQuery()->getResult();
                            //var_dump($locRegionData);
                            $locZoneLMArray = [];
                            foreach ($locZoneLMData as $loc) {
                                $locZoneLMArray["" . $loc["id"]] = $loc["nom"];
                            }

                                //get list of outre-mer
                            //var_dump($localisationArray);
                            $portee_geo = [
                                [
                                    'label' => 'National',
                                    'key' => 'national',
                                    'value' => $locNationalArray
                                ],
                                [
                                    'label' => 'Regional',
                                    'key' => 'region',
                                    'value' => $locRegionArray
                                ],
                                [
                                    'label' => 'Départementale',
                                    'key' => 'departement',
                                    'value' => $locDepartmentArray
                                ],
                                [
                                    'label' => 'Communale (ville)',
                                    'key' => 'commune',
                                    'value' => 'cp'
                                ],
                                [
                                    'label' => 'Zones littorales et maritimes',
                                    'key' => 'ocean',
                                    'value' => $locZoneLMArray
                                ],
                                [
                                    'label' => 'Outre-Mer',
                                    'key' => 'dom',
                                    'value' => $locDOMArray
                                ]
                            ];

                            $localisationsIDArray = [];
                            $type;

                            if (method_exists($formMapper, 'getData')) {

                                $entity = $formMapper->getData();

                            } else if (method_exists($formMapper, 'getAdmin')) {

                                $entity = $formMapper->getAdmin()->getSubject();

                            } else {
                                $entity = null;
                            }

                            foreach( $entity->getLocalisations() as $localisation ) {
                                array_push($localisationsIDArray, $localisation->getId());
                                $type = $localisation->getType();
                                $code = $localisation->getCp();
                            }
                            
                            if(isset($type)) {
                                for($i = 0; $i < count($portee_geo); $i++ ) {
                                    if($portee_geo[$i]['key'] === $type) {
                                        if($type === 'commune') {
                                            $portee_geo[$i]['check'] = $code;
                                            $portee_geo[$i]['isCommune'] = true;
                                        } else {
                                            $portee_geo[$i]['check'] = $localisationsIDArray;
                                        }
                                        
                                        break;
                                    }
                                }
                            }

                            //var_dump($portee_geo);
                            $options['mapped'] = false;
                            $options['attr'] = [
                                'portee_geo' => $portee_geo
                            ];
                            $options['constraints'] = null;

                            // Ne peut pas avoir de constraints puisque le champs n'est pas mapped, Assert dans P_Program->localisations
                            // $options['constraints'] = array(
                            //     new Constraints\Valid(),
                            //     new Constraints\Count(array('min' => 1, 'groups' => array('program_step2'))),
                            // );
                            $formMapper->add('f_' . $field->getProperty(), LocalisationType::class, $options);
                            //dump($options);
                        }
                        if ($field->getFieldType()->getComponent() === "ImageArray") {
                            $options['entry_type'] = 'AdminBundle\Form\PartnersForm';
                            $options['entry_options'] = array('label' => false);
                            $options['allow_add'] = true;
                            $options['allow_delete'] = true;
                            $options['by_reference'] = false;
                            // $options['prototype'] = true;
                            $options['label_attr'] = array_merge($options['label_attr'], array(
                                'class' => $options['label_attr']['class'] . ' col-xs-12'
                            ));

                            $formMapper->add('partners', PdfsType::class, $options);
                        }
                        if ($field->getFieldType()->getComponent() === "FileArray") {
                            $options['entry_type'] = 'AdminBundle\Form\PdfsForm';
                            //$options['entry_options'] = array('label' => false);
                            $options['allow_add'] = true;
                            $options['allow_delete'] = true;
                            $options['by_reference'] = false;
                            // $options['prototype'] = true;
                            $options['label_attr'] = array_merge($options['label_attr'], array(
                                'class' => $options['label_attr']['class'] . ' col-xs-12'
                            ));

                            $formMapper->add($field->getProperty(), FileArrayType::class, $options);
                        }
                    }
                } else {
                    //MESSAGE ERREUR TO LOG
                    $formMapper->add("error", null, array(
                        "mapped" => false,
                        "label" => $options['label'] . ' ########### ' . $errormessage
                    ));
                }
            }
        }
        //$formMapper->end();
        return $formMapper;
    }
}
