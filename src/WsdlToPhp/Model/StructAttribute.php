<?php

use WsdlToPhp\Model\AbstractModel;

/**
 * Class StructAttribute stands for an available struct attribute described in the WSDL
 */
class StructAttribute extends AbstractModel
{
    /**
     * Type of the struct attribute
     * @var string
     */
    private $type = '';
    /**
     * Main constructor
     * @see AbstractModel::__construct()
     * @uses StructAttribute::setType()
     * @uses AbstractModel::setOwner()
     * @param string $_name the original name
     * @param string $_type the type
     * @param WsdlToPhpStruct $_wsdlToPhpStruct defines the struct which owns this value
     * @return StructAttribute
     */
    public function __construct($_name,$_type,WsdlToPhpStruct $_wsdlToPhpStruct)
    {
        parent::__construct($_name);
        $this->setType($_type);
        $this->setOwner($_wsdlToPhpStruct);
    }
    /**
     * Returns the comment lines for this attribute
     * @see AbstractModel::getComment()
     * @uses AbstractModel::getName()
     * @uses WsdlToPhpStruct::getIsStruct()
     * @uses StructAttribute::getType()
     * @uses StructAttribute::getOwner()
     * @uses AbstractModel::addMetaComment()
     * @uses AbstractModel::getModelByName()
     * @uses AbstractModel::getPackagedName()
     * @uses AbstractModel::getInheritance()
     * @return array
     */
    public function getComment()
    {
        $comments = array();
        array_push($comments,'The ' . $this->getName());
        $this->addMetaComment($comments);
        $model = self::getModelByName($this->getType());
        if($model)
        {
            /**
             * A virtual struct exists only to store meta informations about itself
             * A property for which the data type points to its actual owner class has to be of its native type 
             * So don't add meta informations about a valid struct
             */
            if(!$model->getIsStruct() || $model->getPackagedName() == $this->getOwner()->getPackagedName())
            {
                $model->addMetaComment($comments);
                array_push($comments,'@var ' . ($model->getInheritance()?$model->getInheritance():$this->getType()));
            }
            else
                array_push($comments,'@var ' . $model->getPackagedName());
        }
        else
            array_push($comments,'@var ' . $this->getType());
        return $comments;
    }
    /**
     * Returns the unique name in the current struct (for setters/getters and struct contrusctor array)
     * @uses AbstractModel::getCleanName()
     * @uses AbstractModel::getName()
     * @uses AbstractModel::uniqueName()
     * @uses StructAttribute::getOwner()
     * @return string
     */
    public function getUniqueName()
    {
        return self::uniqueName($this->getCleanName(),$this->getOwner()->getName());
    }
    /**
     * Returns the declaration of the attribute
     * @uses AbstractModel::getCleanName()
     * @return string
     */
    public function getDeclaration()
    {
        return 'public $' . $this->getCleanName() . ';';
    }
    /**
     * Returns the getter name for this attribute
     * @uses StructAttribute::getUniqueName()
     * @return string
     */
    public function getGetterName()
    {
        return 'get' . ucfirst(self::getUniqueName());
    }
    /**
     * Returns the getter name for this attribute
     * @uses StructAttribute::getUniqueName()
     * @return string
     */
    public function getSetterName()
    {
        return 'set' . ucfirst(self::getUniqueName());
    }
    /**
     * Returns the array of lines to declare the getter
     * @uses AbstractModel::getModelByName()
     * @uses AbstractModel::getCleanName()
     * @uses AbstractModel::nameIsClean()
     * @uses AbstractModel::getName()
     * @uses AbstractModel::getPackagedName()
     * @uses WsdlToPhpStruct::getIsStruct()
     * @uses StructAttribute::getType()
     * @uses StructAttribute::getGetterName()
     * @uses StructAttribute::isRequired()
     * @uses StructAttribute::getOwner()
     * @param array $_body
     * @param WsdlToPhpStruct $_struct
     * @return void
     */
    public function getGetterDeclaration(&$_body,WsdlToPhpStruct $_struct)
    {
        $model = self::getModelByName($this->getType());
        $isXml = ($this->getType() == 'DOMDocument');
        /**
         * get() method comment
         */
        $comments = array();
        array_push($comments,'Get ' . $this->getName() . ' value');
        if($isXml)
        {
            array_push($comments,'@uses DOMDocument::loadXML()');
            array_push($comments,'@uses DOMDocument::hasChildNodes()');
            array_push($comments,'@uses DOMDocument::saveXML()');
            array_push($comments,'@uses DOMNode::item()');
            array_push($comments,'@uses ' . $_struct->getPackagedName() . '::' . $this->getSetterName() . '()');
            array_push($comments,'@param bool true or false whether to return XML value as string or as DOMDocument');
        }
        array_push($comments,'@return ' . ($model?(($model->getIsStruct() && $model->getPackagedName() != $this->getOwner()->getPackagedName())?$model->getPackagedName():($model->getInheritance()?$model->getInheritance():$this->getType())):$this->getType()) . ($this->isRequired()?'':'|null'));
        array_push($_body,array(
                                'comment'=>$comments));
        /**
         * get() method body
         */
        array_push($_body,'public function ' . $this->getGetterName() . '(' . ($isXml?'$_asString = true':'') . ')');
        array_push($_body,"{");
        $thisAccess = '';
        if($this->nameIsClean())
            $thisAccess = '$this->' . $this->getName();
        else
            $thisAccess = '$this->{\'' . addslashes($this->getName()) . '\'}';
        /**
         * format XML data
         */
        if($isXml)
        {
            array_push($_body,'if(!empty(' . $thisAccess . ') && !(' . $thisAccess . ' instanceof DOMDocument))');
            array_push($_body,'{');
            array_push($_body,'$dom = new DOMDocument(\'1.0\',\'UTF-8\');');
            array_push($_body,'$dom->formatOutput = true;');
            array_push($_body,'if($dom->loadXML(' . $thisAccess . '))');
            array_push($_body,'{');
            array_push($_body,'$this->' . $this->getSetterName() . '($dom);');
            array_push($_body,'}');
            array_push($_body,'unset($dom);');
            array_push($_body,'}');
        }
        if($isXml)
            array_push($_body,'return ($_asString && (' . $thisAccess . ' instanceof DOMDocument) && ' . $thisAccess . '->hasChildNodes())?' . $thisAccess . '->saveXML(' . $thisAccess . '->childNodes->item(0)):' . $thisAccess . ';');
        else
            array_push($_body,'return ' . $thisAccess . ';');
        array_push($_body,"}");
        unset($model,$isXml,$comments);
    }
    /**
     * Returns the array of lines to declare the setter
     * @uses AbstractModel::getModelByName()
     * @uses AbstractModel::getCleanName()
     * @uses AbstractModel::nameIsClean()
     * @uses AbstractModel::getName()
     * @uses AbstractModel::getPackagedName()
     * @uses AbstractModel::getInheritance()
     * @uses WsdlToPhpStruct::getIsRestriction()
     * @uses WsdlToPhpStruct::isArray()
     * @uses StructAttribute::getType()
     * @uses StructAttribute::getSetterName()
     * @uses StructAttribute::getOwner()
     * @param array $_body
     * @param WsdlToPhpStruct $_struct
     * @return void
     */
    public function getSetterDeclaration(&$_body,WsdlToPhpStruct $_struct)
    {
        $model = self::getModelByName($this->getType());
        /**
         * set() method comment
         */
        $comments = array();
        array_push($comments,'Set ' . $this->getName() . ' value');
        if($model && $model->getIsRestriction() && !$_struct->isArray())
            array_push($comments,'@uses ' . $model->getPackagedName() . '::valueIsValid()');
        if($model)
        {
            if($model->getIsStruct() && $model->getPackagedName() != $this->getOwner()->getPackagedName())
            {
                array_push($comments,'@param ' . $model->getPackagedName() . ' $_' . lcfirst($this->getCleanName()) . ' the ' . $this->getName());
                array_push($comments,'@return ' . $model->getPackagedName());
            }
            else
            {
                array_push($comments,'@param ' . ($model->getInheritance()?$model->getInheritance():$this->getType()) . ' $_' . lcfirst($this->getCleanName()) . ' the ' . $this->getName());
                array_push($comments,'@return ' . ($model->getInheritance()?$model->getInheritance():$this->getType()));
            }
        }
        else
        {
            array_push($comments,'@param ' . $this->getType() . ' $_' . lcfirst($this->getCleanName()) . ' the ' . $this->getName());
            array_push($comments,'@return ' . $this->getType());
        }
        array_push($_body,array(
                                'comment'=>$comments));
        /**
         * set() method body
         */
        array_push($_body,'public function ' . $this->getSetterName() . '($_' . lcfirst($this->getCleanName()) . ')');
        array_push($_body,'{');
        if($model && $model->getIsRestriction() && !$_struct->isArray())
        {
            array_push($_body,'if(!' . $model->getPackagedName() . '::valueIsValid($_' . lcfirst($this->getCleanName()) . '))');
            array_push($_body,'{');
            array_push($_body,'return false;');
            array_push($_body,'}');
        }
        if($this->nameIsClean())
            array_push($_body,'return ($this->' . $this->getName() . ' = $_' . lcfirst($this->getCleanName()) . ');');
        else
            array_push($_body,'return ($this->' . $this->getCleanName() . ' = $this->{\'' . addslashes($this->getName()) . '\'} = $_' . lcfirst($this->getCleanName()) . ');');
        array_push($_body,'}');
        unset($model,$comments);
    }
    /**
     * Returns the type value
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Sets the type value
     * @param string $_type
     * @return string
     */
    public function setType($_type)
    {
        return ($this->type = $_type);
    }
    /**
     * Returns potential default value
     * @uses AbstractModel::getMetaValueFirstSet()
     * @uses AbstractModel::getValueWithinItsType()
     * @uses StructAttribute::getType()
     * @return mixed
     */
    public function getDefaultValue()
    {
        return self::getValueWithinItsType($this->getMetaValueFirstSet(array(
                                                                            'default',
                                                                            'Default',
                                                                            'DefaultValue',
                                                                            'defaultValue',
                                                                            'defaultvalue')),$this->getType());
    }
    /**
     * Returns true or false depending on minOccurs information associated to the attribute
     * @uses AbstractModel::getMetaValueFirstSet()
     * @uses AbstractModel::getMetaValue()
     * @return bool true|false
     */
    public function isRequired()
    {
        return ($this->getMetaValue('use','') === 'required' || $this->getMetaValueFirstSet(array(
                                                                                                'minOccurs',
                                                                                                'minoccurs',
                                                                                                'MinOccurs',
                                                                                                'Minoccurs'),false));
    }
    /**
     * Returns the patern which the value must match
     * @uses AbstractModel::getMetaValueFirstSet()
     * @return string
     */
    public function getPattern()
    {
        return $this->getMetaValueFirstSet(array(
                                                'pattern',
                                                'Pattern',
                                                'match',
                                                'Match'),'');
    }
    /**
     * Returns the owner model object, meaning a WsdlToPhpStruct object
     * @see AbstractModel::getOwner()
     * @uses AbstractModel::getOwner()
     * @return WsdlToPhpStruct
     */
    public function getOwner()
    {
        return parent::getOwner();
    }
    /**
     * Returns class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
