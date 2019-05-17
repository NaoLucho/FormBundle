<?php

namespace Builder\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Menu
 *
 * @ORM\Table(name="f_fieldtype")
 * @ORM\Entity(repositoryClass="Builder\FormBundle\Repository\F_FieldTypeRepository")
 */
class F_FieldType
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="base_type", type="string", length=255, nullable=true)
     */
    private $baseType;

    /**
     * @var string
     *
     * @ORM\Column(name="component", type="string", length=255, nullable=true)
     */
    private $component;

    


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
     * Set name
     *
     * @param string $name
     *
     * @return F_FieldType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set component
     *
     * @param string $component
     *
     * @return F_FieldType
     */
    public function setComponent($component)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Get component
     *
     * @return string
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * Set baseType
     *
     * @param string $baseType
     *
     * @return F_FieldType
     */
    public function setBaseType($baseType)
    {
        $this->baseType = $baseType;

        return $this;
    }

    /**
     * Get baseType
     *
     * @return string
     */
    public function getBaseType()
    {
        return $this->baseType;
    }
}
