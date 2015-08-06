<?php

namespace WsdlToPhp\PackageGenerator\File;

use WsdlToPhp\PackageGenerator\Model\AbstractModel;
use WsdlToPhp\PackageGenerator\Model\Struct as StructModel;
use WsdlToPhp\PackageGenerator\Model\StructValue as StructValueModel;
use WsdlToPhp\PackageGenerator\Container\PhpElement\Constant as ConstantContainer;
use WsdlToPhp\PackageGenerator\Container\PhpElement\Method as MethodContainer;
use WsdlToPhp\PhpGenerator\Element\PhpConstant;
use WsdlToPhp\PhpGenerator\Element\PhpMethod;
use WsdlToPhp\PhpGenerator\Element\PhpAnnotation;
use WsdlToPhp\PhpGenerator\Element\PhpAnnotationBlock;

class StructEnum extends Struct
{
    /**
     * @param ConstantContainer
     */
    protected function getClassConstants(ConstantContainer $constants)
    {
        foreach ($this->getModel()->getValues() as $value) {
            $constants->add(new PhpConstant($value->getCleanName(), $value->getValue()));
        }
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getConstantAnnotationBlock(PhpConstant $constant)
    {
        $block = new PhpAnnotationBlock(array(
            sprintf('Constant for value \'%s\'', $constant->getValue()),
        ));
        if (($value = $this->getModel()->getValue($constant->getValue())) instanceof StructValueModel) {
            $this->defineModelAnnotationsFromWsdl($block, $value);
        }
        $block->addChild(new PhpAnnotation(self::ANNOTATION_RETURN, sprintf('string \'%s\'', $constant->getValue())));
        return $block;
    }
    /**
     * @param MethodContainer
     */
    protected function getClassMethods(MethodContainer $methods)
    {
        $methods->add($this->getEnumMethodValueIsValid());
    }
    /**
     * @return PhpAnnotationBlock|null
     */
    protected function getMethodAnnotationBlock(PhpMethod $method)
    {
        return $this->getEnumValueIsValidAnnotationBlock();
    }
    /**
     * @return PhpMethod
     */
    protected function getEnumMethodValueIsValid()
    {
        $method = new PhpMethod('valueIsValid', array(
            'value',
        ), PhpMethod::ACCESS_PUBLIC, false, true);
        $method->addChild(sprintf('return in_array($value, array(%s), true);', implode(', ', $this->getEnumMethodInArrayValues())));
        return $method;
    }
    /**
     * @return string[]
     */
    protected function getEnumMethodInArrayValues()
    {
        $values = array();
        foreach ($this->getModel()->getValues() as $value) {
            $values[] = sprintf('self::%s', $value->getCleanName());
        }
        return $values;
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getEnumValueIsValidAnnotationBlock()
    {
        $annotationBlock = new PhpAnnotationBlock(array(
            'Return true if value is allowed',
        ));
        foreach ($this->getEnumMethodInArrayValues() as $value) {
            $annotationBlock->addChild(new PhpAnnotation(self::ANNOTATION_USES, $value));
        }
        $annotationBlock
            ->addChild(new PhpAnnotation(self::ANNOTATION_PARAM, 'mixed $value value'))
            ->addChild(new PhpAnnotation(self::ANNOTATION_RETURN, 'bool true|false'));
        return $annotationBlock;
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\File\AbstractModelFile::setModel()
     * @throws \InvalidaArgumentException
     * @param AbstractModel $model
     * @return StructArray
     */
    public function setModel(AbstractModel $model)
    {
        if ($model instanceof StructModel && !$model->getIsRestriction()) {
            throw new \InvalidArgumentException('Model must be a restriction containing values', __LINE__);
        }
        return parent::setModel($model);
    }
}
