<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl;

use WsdlToPhp\PackageGenerator\Model\Schema as Model;
use WsdlToPhp\PackageGenerator\Container\Model\Schema as ModelContainer;

class Wsdl extends AbstractDocument
{
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler::__construct()
     * @param \DOMDocument $domDocument
     * @return Wsdl
     */
    public function __construct(\DOMDocument $domDocument)
    {
        parent::__construct($domDocument);
        $this->externalSchemas = new ModelContainer();
    }
    /**
     * @var ModelContainer
     */
    protected $externalSchemas;
    /**
     * @param Model $schema
     * @return \WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl
     */
    public function addExternalSchema(Model $schema)
    {
        $this->externalSchemas->add($schema);
        return $this;
    }
    /**
     * @param string $name
     * @return \WsdlToPhp\PackageGenerator\Model\Schema|null
     */
    public function getExternalSchema($name)
    {
        return $this->externalSchemas->getSchemaByName($name);
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Container\Model\Schema
     */
    public function getExternalSchemas()
    {
        return $this->externalSchemas;
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\Wsdl\AbstractDocument::getElementByName()
     * @param bool $includeExternals force search among external schemas
     */
    public function getElementByName($name, $includeExternals = false)
    {
        $element = parent::getElementByName($name);
        if ($includeExternals === true && $element === null) {
            $element = $this->useExternalSchemas(__FUNCTION__, array(
                $name,
            ), true);
        }
        return $element;
    }
    /**
     * @param string $method
     * @param array $parameters
     * @param bool $returnOne
     * @return mixed
     */
    public function useExternalSchemas($method, $parameters, $returnOne = false)
    {
        $result = $returnOne === true ? null : array();
        if ($this->getExternalSchemas()->count() > 0) {
            foreach ($this->getExternalSchemas() as $externalSchema) {
                $externalResult = call_user_func_array(array(
                    $externalSchema,
                    $method,
                ), $parameters);
                if ($returnOne === true && $externalResult !== null) {
                    $result = $externalResult;
                    break;
                } elseif (is_array($externalResult)) {
                    $result = array_merge($result, $externalResult);
                }
            }
        }
        return $result;
    }
}
