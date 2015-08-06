<?php

namespace WsdlToPhp\PackageBase\Tests;


use WsdlToPhp\PackageBase\Utils;

use WsdlToPhp\PackageBase\Tests\SoapClient;

class SoapClientTest extends TestCase
{
    /**
     *
     */
    public function testSoapClientName()
    {
        $soapClient = new SoapClient();

        $this->assertSame('\\WsdlToPhp\\PackageBase\\Tests\\Client', $soapClient->getSoapClientClassName());
    }
    /**
     *
     */
    public function testSoapClientNameDefault()
    {
        $soapClient = new SoapClient();

        $this->assertSame('\SoapClient', $soapClient->getSoapClientClassName('\\WsdlToPhp\\PackageBase\\Tests\\Clien'));
    }
    /**
     *
     */
    public function testSoapClient()
    {
        $soapClient = new SoapClient(array(
            SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
            SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertInstanceOf('\\WsdlToPhp\\PackageBase\\Tests\\Client', $soapClient->getSoapClient());
    }
    /**
     *
     */
    public function testSetLocation()
    {
        $soapClient = new SoapClient(array(
            SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
            SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $soapClient->setLocation('http://api.bing.net:80/soap.asm');

        $this->assertSAme('http://api.bing.net:80/soap.asm', $soapClient->getSoapClient()->location);
    }
    /**
     *
     */
    public function testGetLastRequestAsString()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertSame(Utils::getFormatedXml(file_get_contents(__DIR__ . '/resources/oneline.xml')), $soapClient->getLastRequest(false));
    }
    /**
     *
     */
    public function testGetLastRequestAsDomDocument()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertInstanceOf('\\DOMDocument', $soapClient->getLastRequest(true));
    }
    /**
     *
     */
    public function testGetLastResponseAsString()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertSame(Utils::getFormatedXml(file_get_contents(__DIR__ . '/resources/oneline.xml')), $soapClient->getLastResponse(false));
    }
    /**
     *
     */
    public function testGetLastResponseAsDomDocument()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertInstanceOf('\\DOMDocument', $soapClient->getLastResponse(true));
    }
    /**
     *
     */
    public function testGetLastRequestHeadersAsString()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertSame($soapClient->getSoapClient()->__getLastRequestHeaders(), $soapClient->getLastRequestHeaders(false));
    }
    /**
     *
     */
    public function testGetLastRequestHeadersAsArray()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertSame($soapClient->getSoapClient()->getLastRequestHeadersAsArray(), $soapClient->getLastRequestHeaders(true));
    }
    /**
     *
     */
    public function testGetLastResponseHeadersAsString()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertSame($soapClient->getSoapClient()->__getLastResponseHeaders(), $soapClient->getLastResponseHeaders(false));
    }
    /**
     *
     */
    public function testGetLastResponseHeadersAsArray()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertSame($soapClient->getSoapClient()->getLastResponseHeadersAsArray(), $soapClient->getLastResponseHeaders(true));
    }
    /**
     *
     */
    public function testSetGetLastErrorForMethod()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        // this call should fail as no parameter is defined in the request
        $soapClient->search();

        $this->assertInstanceOf('\\SoapFault', $soapClient->getLastErrorForMethod('WsdlToPhp\PackageBase\Tests\SoapClient::search'));
    }
    /**
     *
     */
    public function testSetGetLastError()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        // this call should fail as no parameter is defined in the request
        $soapClient->search();

        $this->assertCount(1, $soapClient->getLastError());
    }
    /**
     *
     */
    public function testSetGetResult()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        // this call should fail as no parameter is defined in the request
        $soapClient->search();

        $this->assertNull($soapClient->getResult());
    }
    /**
     *
     */
    public function testSetHeaders()
    {
        $soapClient = new SoapClient(array(
                SoapClient::WSDL_URL => __DIR__ . '/resources/bingsearch.wsdl',
                SoapClient::WSDL_CLASSMAP => self::classMap(),
        ));

        $this->assertTrue($soapClient->setHttpHeader('X-Header-Name', 'X-Header-Value'));

        $this->assertTrue(is_resource(SoapClient::getSoapClient()->_stream_context));

        $this->assertSame(array(
            'http' => array(
                'header' => 'X-Header-Name: X-Header-Value',
            ),
        ), stream_context_get_options(SoapClient::getSoapClient()->_stream_context));
    }
    /**
     * @return string[]
     */
    public static function classMap()
    {
        return array (
            'AdultOption' => 'ApiEnumAdultOption',
            'ArrayOfDeepLink' => 'ApiStructArrayOfDeepLink',
            'ArrayOfError' => 'ApiStructArrayOfError',
            'ArrayOfImageResult' => 'ApiStructArrayOfImageResult',
            'ArrayOfInstantAnswerResult' => 'ApiStructArrayOfInstantAnswerResult',
            'ArrayOfMobileWebResult' => 'ApiStructArrayOfMobileWebResult',
            'ArrayOfMobileWebSearchOption' => 'ApiStructArrayOfMobileWebSearchOption',
            'ArrayOfNewsArticle' => 'ApiStructArrayOfNewsArticle',
            'ArrayOfNewsCollection' => 'ApiStructArrayOfNewsCollection',
            'ArrayOfNewsRelatedSearch' => 'ApiStructArrayOfNewsRelatedSearch',
            'ArrayOfNewsResult' => 'ApiStructArrayOfNewsResult',
            'ArrayOfRelatedSearchResult' => 'ApiStructArrayOfRelatedSearchResult',
            'ArrayOfSearchOption' => 'ApiStructArrayOfSearchOption',
            'ArrayOfSourceType' => 'ApiStructArrayOfSourceType',
            'ArrayOfSpellResult' => 'ApiStructArrayOfSpellResult',
            'ArrayOfString' => 'ApiStructArrayOfString',
            'ArrayOfVideoResult' => 'ApiStructArrayOfVideoResult',
            'ArrayOfWebResult' => 'ApiStructArrayOfWebResult',
            'ArrayOfWebSearchOption' => 'ApiStructArrayOfWebSearchOption',
            'ArrayOfWebSearchTag' => 'ApiStructArrayOfWebSearchTag',
            'DeepLink' => 'ApiStructDeepLink',
            'Error' => 'ApiStructError',
            'ImageRequest' => 'ApiStructImageRequest',
            'ImageResponse' => 'ApiStructImageResponse',
            'ImageResult' => 'ApiStructImageResult',
            'InstantAnswerResponse' => 'ApiStructInstantAnswerResponse',
            'InstantAnswerResult' => 'ApiStructInstantAnswerResult',
            'MobileWebRequest' => 'ApiStructMobileWebRequest',
            'MobileWebResponse' => 'ApiStructMobileWebResponse',
            'MobileWebResult' => 'ApiStructMobileWebResult',
            'MobileWebSearchOption' => 'ApiEnumMobileWebSearchOption',
            'NewsArticle' => 'ApiStructNewsArticle',
            'NewsCollection' => 'ApiStructNewsCollection',
            'NewsRelatedSearch' => 'ApiStructNewsRelatedSearch',
            'NewsRequest' => 'ApiStructNewsRequest',
            'NewsResponse' => 'ApiStructNewsResponse',
            'NewsResult' => 'ApiStructNewsResult',
            'NewsSortOption' => 'ApiEnumNewsSortOption',
            'PhonebookRequest' => 'ApiStructPhonebookRequest',
            'PhonebookSortOption' => 'ApiEnumPhonebookSortOption',
            'Query' => 'ApiStructQuery',
            'RelatedSearchResponse' => 'ApiStructRelatedSearchResponse',
            'RelatedSearchResult' => 'ApiStructRelatedSearchResult',
            'SearchOption' => 'ApiEnumSearchOption',
            'SearchRequest' => 'ApiStructSearchRequest',
            'SearchResponse' => 'ApiStructSearchResponse',
            'SourceType' => 'ApiEnumSourceType',
            'SpellResponse' => 'ApiStructSpellResponse',
            'SpellResult' => 'ApiStructSpellResult',
            'Thumbnail' => 'ApiStructThumbnail',
            'TranslationRequest' => 'ApiStructTranslationRequest',
            'VideoRequest' => 'ApiStructVideoRequest',
            'VideoResponse' => 'ApiStructVideoResponse',
            'VideoResult' => 'ApiStructVideoResult',
            'VideoSortOption' => 'ApiEnumVideoSortOption',
            'WebRequest' => 'ApiStructWebRequest',
            'WebResponse' => 'ApiStructWebResponse',
            'WebResult' => 'ApiStructWebResult',
            'WebSearchOption' => 'ApiEnumWebSearchOption',
            'WebSearchTag' => 'ApiStructWebSearchTag',
        );
    }
}
