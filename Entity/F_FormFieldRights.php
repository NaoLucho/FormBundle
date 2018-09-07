<?php

namespace Builder\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Menu
 *
 * @ORM\Table(name="f_formfieldrights")
 * @ORM\Entity(repositoryClass="Builder\FormBundle\Repository\F_FormFieldRightsRepository")
 */
class F_FormFieldRights
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
    * @ORM\ManyToOne(targetEntity="Builder\FormBundle\Entity\F_FormField", inversedBy="formfieldrights")
    * @ORM\JoinColumn(name="formfield_id", referencedColumnName="id", nullable=false)
    */
    protected $formField;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    private $rights;


    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=255)
     */
    private $mode = 'VIEW';

    

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
     * Set mode
     *
     * @param string $mode
     *
     * @return F_FormFieldRights
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set formField
     *
     * @param \Builder\FormBundle\Entity\F_FormField $formField
     *
     * @return F_FormFieldRights
     */
    public function setFormField(\Builder\FormBundle\Entity\F_FormField $formField)
    {
        $this->formField = $formField;

        return $this;
    }

    /**
     * Get formField
     *
     * @return \Builder\FormBundle\Entity\F_FormField
     */
    public function getFormField()
    {
        return $this->formField;
    }

    /**
     * Set rights
     *
     * @param \Application\Sonata\UserBundle\Entity\Group $rights
     *
     * @return F_FormFieldRights
     */
    public function setRights(\Application\Sonata\UserBundle\Entity\Group $rights)
    {
        $this->rights = $rights;

        return $this;
    }

    /**
     * Get rights
     *
     * @return \Application\Sonata\UserBundle\Entity\Group
     */
    public function getRights()
    {
        return $this->rights;
    }
}
