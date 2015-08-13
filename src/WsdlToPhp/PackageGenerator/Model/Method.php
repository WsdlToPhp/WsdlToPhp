<?php

namespace WsdlToPhp\PackageGenerator\Model;

use WsdlToPhp\PackageGenerator\Generator\Generator;

/**
 * Class Method stands for an available operation described in the WSDL
 */
class Method extends AbstractModel
{
    /**
     * Type of the parameter for the operation
     * @var string
     */
    private $parameterType = '';
    /**
     * Type of the return value for the operation
     * @var string
     */
    private $returnType = '';
    /**
     * Indicates function is not alone with this name, then its name is contextualized based on its parameter(s)
     * @var bool
     */
    private $isUnique = true;
    /**
     * Generated method name stored as soon as it has been defined once
     * @var string
     */
    private $methodName = null;
    /**
     * Main constructor
     * @see AbstractModel::__construct()
     * @uses Method::setParameterType()
     * @uses Method::setReturnType()
     * @uses AbstractModel::setOwner()
     * @param Generator $generator
     * @param string $name the function name
     * @param string|array $parameterType the type/name of the parameter
     * @param string|array $returnType the type/name of the return value
     * @param Service $service defines the struct which owns this value
     * @param bool $isUnique defines if the method is unique or not
     */
    public function __construct(Generator $generator, $name, $parameterType, $returnType, Service $service, $isUnique = true)
    {
        parent::__construct($generator, $name);
        $this
            ->setParameterType($parameterType)
            ->setReturnType($returnType)
            ->setIsUnique($isUnique)
            ->setOwner($service);
    }
    /**
     * Returns the name of the method that is used to call the operation
     * It takes care of the fact that the method might not be the only one named as it is.
     * @uses AbstractModel::getCleanName()
     * @uses AbstractModel::replaceReservedPhpKeyword()
     * @uses AbstractModel::getOwner()
     * @uses AbstractModel::getPackagedName()
     * @uses AbstractModel::uniqueName()
     * @uses Method::getOwner()
     * @uses Method::getParameterType()
     * @uses Method::getIsUnique()
     * @return string
     */
    public function getMethodName()
    {
        if (empty($this->methodName)) {
            $methodName = $this->getCleanName();
            if (!$this->getIsUnique()) {
                if (is_string($this->getParameterType())) {
                    $methodName .= ucfirst($this->getParameterType());
                } else {
                    $methodName .= '_' . md5(var_export($this->getParameterType(), true));
                }
            }
            $methodName = self::replaceReservedPhpKeyword($methodName, $this->getOwner()->getPackagedName());
            $this->methodName = self::uniqueName($methodName, $this->getOwner()->getPackagedName());
        }
        return $this->methodName;
    }
    /**
     * Returns the parameter type
     * @return string|string[]
     */
    public function getParameterType()
    {
        return $this->parameterType;
    }
    /**
     * Set the parameter type
     * @param string|string[]
     * @return Method
     */
    public function setParameterType($parameterType)
    {
        $this->parameterType = $parameterType;
        return $this;
    }
    /**
     * Returns the retrun type
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }
    /**
     * Set the retrun type
     * @param string|string[]
     * @return Method
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
        return $this;
    }
    /**
     * Returns the isUnique property
     * @return bool
     */
    public function getIsUnique()
    {
        return $this->isUnique;
    }
    /**
     * Set the isUnique property
     * @param bool
     * @return Method
     */
    public function setIsUnique($isUnique)
    {
        $this->isUnique = $isUnique;
        return $this;
    }
    /**
     * Returns the owner model object, meaning a Service object
     * @see AbstractModel::getOwner()
     * @uses AbstractModel::getOwner()
     * @return Service
     */
    public function getOwner()
    {
        return parent::getOwner();
    }
}
