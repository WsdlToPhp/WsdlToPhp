<?php
namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\Model\Struct;
use WsdlToPhp\PackageGenerator\Model\Method;
use WsdlToPhp\PackageGenerator\Generator\Generator;

abstract class AbstractTagParser extends AbstractParser
{
    /**
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }
    /**
     * Return the model on which the method will be called
     * @param Tag $tag
     * @return Struct|Method
     */
    protected function getModel(Tag $tag)
    {
        $model = null;
        switch ($tag->getName()) {
            case WsdlDocument::TAG_OPERATION:
                $model = $this->generator->getServiceMethod($tag->getAttributeName());
                break;
            default:
                $model = $this->generator->getStruct($tag->getAttributeName());
                break;
        }
        return $model;
    }
}