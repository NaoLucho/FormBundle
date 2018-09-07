<?php

namespace Builder\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Menu
 *
 * @ORM\Table(name="f_field")
 * @ORM\Entity(repositoryClass="Builder\FormBundle\Repository\F_FieldRepository")
 */
class F_Field
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="property", type="string", length=255)
     */
    private $property;

    /**
    * @ORM\ManyToOne(targetEntity="Builder\FormBundle\Entity\F_FieldType")
    * @ORM\JoinColumn(name="fieldtype_id", referencedColumnName="id")
    */
    private $fieldtype;

    /**
     * @var string
     *
     * @ORM\Column(name="listname", type="string", length=255, nullable=true)
     */
    private $listname;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="placeholder", type="string", length=512, nullable=true)
     */
    private $placeHolder;

    /**
     * @var bool
     *
     * @ORM\Column(name="mandatory", type="boolean", nullable=true)
     */
    private $mandatory = false;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=1024, nullable=true)
     */
    private $info;

    /**
     * @var integer
     *
     * @ORM\Column(name="limitnb", type="integer", nullable=true)
     */
    private $limit;

    /**
    * @ORM\OneToMany(targetEntity="Builder\FormBundle\Entity\F_FormField", mappedBy="field", cascade={"persist"})
    */
    protected $formFields;

    // /**
    // * @ORM\ManyToOne(targetEntity="Builder\ListBundle\Entity\G_List")
    // * @ORM\JoinColumn(name="list_id", referencedColumnName="id")
    // */
    // private $gList;
        
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->formFields = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set property
     *
     * @param string $property
     *
     * @return F_Field
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return F_Field
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set mandatory
     *
     * @param boolean $mandatory
     *
     * @return F_Field
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * Get mandatory
     *
     * @return boolean
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return F_Field
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Add formField
     *
     * @param \Builder\FormBundle\Entity\F_FormField $formField
     *
     * @return F_Field
     */
    public function addFormField(\Builder\FormBundle\Entity\F_FormField $formField)
    {
        $this->formFields[] = $formField;

        return $this;
    }

    /**
     * Remove formField
     *
     * @param \Builder\FormBundle\Entity\F_FormField $formField
     */
    public function removeFormField(\Builder\FormBundle\Entity\F_FormField $formField)
    {
        $this->formFields->removeElement($formField);
    }

    /**
     * Get formFields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormFields()
    {
        return $this->formFields;
    }

    /**
     * Set fieldtype
     *
     * @param \Builder\FormBundle\Entity\F_FieldType $fieldtype
     *
     * @return F_Field
     */
    public function setFieldtype(\Builder\FormBundle\Entity\F_FieldType $fieldtype = null)
    {
        $this->fieldtype = $fieldtype;

        return $this;
    }

    /**
     * Get fieldtype
     *
     * @return \Builder\FormBundle\Entity\F_FieldType
     */
    public function getFieldtype()
    {
        return $this->fieldtype;
    }

    /**
     * Set placeHolder
     *
     * @param string $placeHolder
     *
     * @return F_Field
     */
    public function setPlaceHolder($placeHolder)
    {
        $this->placeHolder = $placeHolder;

        return $this;
    }

    /**
     * Get placeHolder
     *
     * @return string
     */
    public function getPlaceHolder()
    {
        return $this->placeHolder;
    }

    /**
     * Set listname
     *
     * @param string $listname
     *
     * @return F_Field
     */
    public function setListname($listname)
    {
        $this->listname = $listname;

        return $this;
    }

    /**
     * Get listname
     *
     * @return string
     */
    public function getListname()
    {
        return $this->listname;
    }

    /**
     * Set limit
     *
     * @param string $limit
     *
     * @return F_FormField
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get limit
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
