<?php

namespace BingApi\ServiceType;

use \WsdlToPhp\PackageBase\AbstractSoapClientBase;

/**
 * This class stands for Search ServiceType
 * @package BingApi
 * @subpackage Services
 * @release 1.1.0
 */
class SearchBingApi extends AbstractSoapClientBase
{
    /**
     * Method to call the operation originally named Search
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param \BingApi\StructType\SearchRequestBingApi $parameters
     * @return \BingApi\StructType\SearchResponseBingApi|bool
     */
    public function Search(\BingApi\StructType\SearchRequestBingApi $parameters)
    {
        try {
            $this->setResult(self::getSoapClient()->Search($parameters));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Returns the result
     * @see AbstractSoapClientBase::getResult()
     * @return \BingApi\StructType\SearchResponseBingApi
     */
    public function getResult()
    {
        return parent::getResult();
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
