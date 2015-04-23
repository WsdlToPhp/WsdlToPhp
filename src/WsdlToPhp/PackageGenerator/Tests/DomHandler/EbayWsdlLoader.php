<?php

namespace WsdlToPhpGenerator\PackageGenerator\Tests\DomHandler;

class EbayWsdlLoader
{
    /**
     * @var \DOMDocument
     */
    protected $domDocument;
    public function __construct()
    {
        $this->domDocument = new \DOMDocument('1.0', 'utf-8');
        $this->domDocument->load(dirname(__FILE__) . '/../resources/ebaySvc.wsdl');
    }
    /**
     * @param string $name
     * @return \DOMNode
     */
    public function getOneNode($name)
    {
        return $this->domDocument->getElementsByTagName($name)->item(0);
    }
    /**
     * @param string $name
     * @return \DOMNodeList
     */
    public function getNodes($name)
    {
        return $this->domDocument->getElementsByTagName($name);
    }
}
