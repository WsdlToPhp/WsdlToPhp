<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Tests\TestCase;

class WsdlParser extends TestCase
{
    /**
     * @return string
     */
    public static function wsdlPartnerPath()
    {
        return dirname(__FILE__) . '/../../resources/PartnerService.local.wsdl';
    }
    /**
     * @return string
     */
    public static function schemaPartnerPath()
    {
        return dirname(__FILE__) . '/../../resources/PartnerService.0.xsd';
    }
    /**
     * @return string
     */
    public static function wsdlImageViewServicePath()
    {
        return dirname(__FILE__) . '/../../resources/ImageViewService.local.wsdl';
    }
    /**
     * @return string
     */
    public static function schemaImageViewServicePath()
    {
        return dirname(__FILE__) . '/../../resources/imageViewCommon.xsd';
    }
}
