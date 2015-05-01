<?php

namespace WsdlToPhp\PackageGenerator\Generator;

use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\AbstractModel;
use WsdlToPhp\PackageGenerator\Model\EmptyModel;
use WsdlToPhp\PackageGenerator\Model\Struct;
use WsdlToPhp\PackageGenerator\Model\StructAttribute;
use WsdlToPhp\PackageGenerator\Model\StructValue;
use WsdlToPhp\PackageGenerator\Model\Service;
use WsdlToPhp\PackageGenerator\Model\Method;
use WsdlToPhp\PackageGenerator\ConfigurationReader\GeneratorOptions;
use WsdlToPhp\PackageGenerator\Container\Model\Wsdl as WsdlContainer;
use WsdlToPhp\PackageGenerator\Container\Model\Struct as StructContainer;
use WsdlToPhp\PackageGenerator\Container\Model\Service as ServiceContainer;
use WsdlToPhp\PackageGenerator\Parser\SoapClient\Structs as StructsParser;
use WsdlToPhp\PackageGenerator\Parser\SoapClient\Functions as FunctionsParser;

/**
 * Class Generator
 * This class replaces the original WsdlToPhp class.
 * It uses the AbstractModel's classes (Struct, Service, Method, StructAttribute, StructValue) in order to rationalize informations.
 * From now, each class is clearly identified depending on its behaviour :
 * <ul>
 * <li>{PackageName}Service* : class which gathers the operations/functions (based on their name)</li>
 * <li>{PackageName}Struct* : class which represents a struct type which can be used either for requesting or catching response</li>
 * <li>{PackageName}Enum* : class which represents an enumeration of values. Each value is defined with a constant</li>
 * <li>{PackageName}WsdlClass : mother class of all generated class if enabled. This class defines all the generic methods and the needed configurations/methods to call the SOAP WS</li>
 * <li>{PackageName}ClassMap : class that constains one final public static method which returns the array to map structs/enums to generated classes</li>
 * </ul>
 * Test case examples
 * <ul>
 * <li>
 * "Full" documentation (functions, structs, enumerations, values) with "virual" structs inheritance documentation :
 * <ul>
 * <li>{@link http://developer.ebay.com/webservices/latest/ebaySvc.wsdl}</li>
 * <li>{@link https://www.paypalobjects.com/wsdl/PayPalSvc.wsdl}</li>
 * <li>{@link http://queue.amazonaws.com/doc/2012-11-05/QueueService.wsdl}</li>
 * <li>{@link https://xhi.venere.com/xhi-1.0/services/OTA_ReadNotifReport.soap?wsdl}</li>
 * </ul>
 * </li>
 * <li>Restriction on struct attributes :
 * <ul>
 * <li>{@link https://services.pwsdemo.com/WSDL/PwsDemo_creditcardtransactionservice.xml}</li>
 * <li>{@link http://api.temando.com/schema/2009_06/server.wsdl}</li>
 * <li>{@link http://info.portaldasfinancas.gov.pt/NR/rdonlyres/02357996-29FC-4F11-9F1D-6EA2B9210D60/0/factemiws.wsdl}</li>
 * </ul>
 * </li>
 * <li>Operations or struct attributes with an illegal character (ex : ., -) :
 * <ul>
 * <li>{@link http://api.fromdoppler.com/Default.asmx?WSDL}</li>
 * <li>{@link https://webapi.aukro.cz/uploader.php?wsdl}</li>
 * <li>{@link https://www.paypalobjects.com/wsdl/PayPalSvc.wsdl}</li>
 * </ul>
 * </li>
 * <li>Simple function parameter (not a struct) :
 * <ul>
 * <li>{@link http://traveltek-importer.planetcruiseluxury.co.uk/region.wsdl}</li>
 * </ul>
 * </li>
 * <li>Enumerations with two similar values (ex : y and Y in RecurringFlagType) :
 * <ul>
 * <li>{@link https://www.paypalobjects.com/wsdl/PayPalSvc.wsdl}</li>
 * </ul>
 * </li>
 * <li>Enumerations embedded in an element :
 * <ul>
 * <li>{@link http://92.70.240.139/webservices_test/?WSDL}</li>
 * </ul>
 * </li>
 * <li>Lots of import tags :
 * <ul>
 * <li>{@link http://secapp.euroconsumers.org/partnerservice/PartnerService.svc?wsdl}</li>
 * <li>{@link https://webservices.netsuite.com/wsdl/v2012_2_0/netsuite.wsdl}</li>
 * <li>{@link http://mobile.esseginformatica.com:8704/?wsdl}</li>
 * <li>{@link https://api.bullhornstaffing.com/webservices-2.5/?wsdl}</li>
 * <li>{@link http://46.31.56.162/abertis/Sos.asmx?WSDL}</li>
 * <li>{@link http://www.reservationfactory.com/wsdls/air_v21_0/Air.wsdl} with relative paths like ../ which causes bugs</li>
 * </ul>
 * </li>
 * <li>"Deep", numerous inheritance in struct classes :
 * <ul>
 * <li>{@link https://moa.mazdaeur.com/mud-services/ws/PartnerService?wsdl}</li>
 * <li>{@link http://developer.ebay.com/webservices/latest/ebaySvc.wsdl}</li>
 * <li>{@link https://www.tipsport.cz/webtip/CommonBettingWS?WSDL}</li>
 * <li>{@link https://www.tipsport.cz/webtip/LiveBettingWS?WSDL}</li>
 * <li>{@link https://webservices.netsuite.com/wsdl/v2012_2_0/netsuite.wsdl}</li>
 * <li>{@link https://www.paypalobjects.com/wsdl/PayPalSvc.wsdl}</li>
 * <li>{@link http://mobile.esseginformatica.com:8704/?wsdl}</li>
 * <li>{@link https://api.bullhornstaffing.com/webservices-2.5/?wsdl}</li>
 * <li>{@link http://46.31.56.162/abertis/Sos.asmx?WSDL} (real deep inheritance from AbstractGMLType)</li>
 * <li>{@link http://securedev.sedagroup.com.au/ws/jadehttp.dll?SOS&listName=SedaWebService&serviceName=SedaWebServiceProvider&wsdl=wsdl}</li>
 * <li>{@link https://raw.github.com/jkinred/psphere/master/psphere/wsdl/vimService.wsdl}</li>
 * <li>{@link http://staging.timatic.aero/timaticwebservices/timatic3.WSDL}</li>
 * <li>{@link http://www.reservationfactory.com/wsdls/air_v21_0/Air.wsdl}</li>
 * <li>{@link http://voipnow2demo.4psa.com//soap2/schema/3.0.0/voipnowservice.wsdl}</li>
 * </ul>
 * </li>
 * <li>Multiple service operations returns the same response type (getResult() doc comment must return one type of each) :
 * <ul>
 * <li>{@link https://secure.dev.logalty.es/lgt/logteca/emisor/services/IncomingServiceWSI?wsdl}</li>
 * <li>{@link http://partners.a2zinc.net/dataservices/public/exhibitorprovider.asmx?WSDL}</li>
 * </ul>
 * </li>
 * <li>Documentation on WSDL (must be found in the generated *WsdlClass) doc comment :
 * <ul>
 * <li>{@link http://iplaypen.globelabs.com.ph:1881/axis2/services/Platform?wsdl}</li>
 * <li>{@link https://oivs.mvtrip.alabama.gov/service/XMLExchangeServiceCore.asmx?WSDL}</li>
 * </ul>
 * </li>
 * <li>PHP reserved keyword in operation name (ex : list, add), replaced by _{keyword} :
 * <ul>
 * <li>{@link https://api5.successfactors.eu/sfapi/v1/soap12?wsdl}</li>
 * <li>{@link https://webservices.netsuite.com/wsdl/v2012_2_0/netsuite.wsdl}</li>
 * </ul>
 * </li>
 * <li>Send ArrayAsParameter and ParametersAsArray case :
 * <ul>
 * <li>{@link http://api.bing.net/search.wsdl}</li>
 * </ul>
 * </li>
 * <li>Send parameters separately :
 * <ul>
 * <li>{@link http://www.ovh.com/soapi/soapi-dlw-1.54.wsdl}</li>
 * </ul>
 * </li>
 * <li>Struct attribute named _ :
 * <ul>
 * <li>{@link http://46.31.56.162/abertis/Sos.asmx?WSDL} (DirectPositionType, StringOrRefType, AreaType, etc.)</li>
 * </ul>
 * </li>
 * <li>From now, it can generate service function from RPC style SOAP WS. RPC style :
 * <ul>
 * <li>{@link http://www.ovh.com/soapi/soapi-re-1.54.wsdl}</li>
 * <li>{@link http://postlinks.com/api/soap/v1.1/postlinks.wsdl}</li>
 * <li>{@link http://psgsa.dyndns.org:8020/gana/crm/service/v4_1/soap.php?wsdl}</li>
 * <li>{@link http://www.electre.com/WebService/search.asmx?WSDL}</li>
 * <li>{@link http://www.mobilefish.com/services/web_service/countries.php?wsdl}</li>
 * <li>{@link http://webservices.seek.com.au/FastLanePlus.asmx?WSDL}</li>
 * <li>{@link https://webapi.aukro.cz/uploader.php?wsdl}</li>
 * <li>{@link http://castonclients.com/fuelcircle/api/member.php?wsdl}</li>
 * <li>{@link https://www.fieldnation.com/api/v3.5/fieldnation.wsdl}</li>
 * <li>{@link https://gateway2.pagosonline.net/ws/WebServicesClientesUT?wsdl}</li>
 * <li>{@link http://webservices.seek.com.au/webserviceauthenticator.asmx?WSDL}</li>
 * <li>{@link http://webservices.seek.com.au/fastlaneplus.asmx?WSDL}</li>
 * <li>{@link https://80.77.87.229:2443/soap?wsdl}</li>
 * <li>{@link http://www.mantisbt.org/demo/api/soap/mantisconnect.php?wsdl}</li>
 * <li>{@link http://mira.16.1.t1.connectivegames.com/axis/services/RemoteAffiliateService?wsdl}</li>
 * <li>{@link http://graphical.weather.gov/xml/DWMLgen/wsdl/ndfdXML.wsdl}</li>
 * <li>{@link https://www.mygate.co.za/enterprise/4x0x0/ePayService.cfc?wsdl}</li>
 * <li>{@link http://59.162.33.102/HotelXML_V1.2/services/HotelAvailSearch?wsdl}</li>
 * <li>{@link http://api.4shared.com/jax2/DesktopApp?wsdl}</li>
 * <li>{@link http://www.eaglepictures.com/services.php/Eagle.wsdl}</li>
 * <li>{@link http://online.axiomtelecom.com/staging/api/v2_soap/?wsdl}</li>
 * <li>{@link http://www.konakart.com/konakart/services/KKWebServiceEng?wsdl}</li>
 * <li>{@link http://soamoa.org:9292/artistRegistry?WSDL}</li>
 * <li>{@link http://pgw-dev.aora.tv/trio/index.php?wsdl}</li>
 * <li>{@link http://npg.dl.ac.uk/MIDAS/MIDASWebServices/MIDASWebServices/VMEAccessServer.wsdl}</li>
 * </ul>
 * </li>
 * <li>From now, method without any parameter are generated well. A method without any parameter :
 * <ul>
 * <li>{@link http://verkopen.marktplaats.nl/soap/mpplt.php?wsdl} (GetCategoryList, GetCategoryListRevision, GetPriceTypeList, GetPriceTypeListRevision, GetAttributeListRevision, GetSystemTimestamp)</li>
 * <li>{@link https://gateway2.pagosonline.net/ws/WebServicesClientesUT?wsdl} (doInit(), TestServiceGet, TestServiceLeer)</li>
 * </ul>
 * </li>
 * <li>Operation name with illegal characters :
 * <ul>
 * <li>{@link https://raw.github.com/Sn3b/Omniture-API/master/Solution%20Items/OmnitureAdminServices.wsdl} (. in the operation name, so __soapCall method is used)</li>
 * </ul>
 * </li>
 * <li>Struct attributes with same but different case (ProcStat and Procstat) should have distinct method to set and get (getProcStat/setProcStat and getProcstat_1/setProcstat_1) the value.</li>
 * <li>The contruct method must also define the key in the associative array with the corresponding method name. Plus, the operation/function which use ths attribute must call the distinct method (getProcStat and getProcstat_1). See {@link http://the-echoplex.net/log/php-case-sensitivity}</li>
 * <li>Catch SOAPHeader definitions :
 * <ul>
 * <li>{@link http://164.9.104.198/dhlexpress4youservice/Express4YouService.svc?wsdl}</li>
 * <li>{@link http://api.atinternet-solutions.com/toolbox/reporting.asmx?WSDL}</li>
 * <li>{@link http://developer.ebay.com/webservices/latest/ebaySvc.wsdl}</li>
 * <li>{@link https://www.paypalobjects.com/wsdl/PayPalSvc.wsdl}</li>
 * <li>{@link https://webservices.netsuite.com/wsdl/v2012_2_0/netsuite.wsdl}</li>
 * <li>{@link http://securedev.sedagroup.com.au/ws/jadehttp.dll?SOS&listName=SedaWebService&serviceName=SedaWebServiceProvider&wsdl=wsdl}</li>
 * <li>{@link http://api.actonsoftware.com/soap/services/ActonService2?wsdl}, multiple header for a given operation</li>
 * <li>{@link http://webservices.eurotaxglass.com/wsdl/forecast.wsdl}</li>
 * <li>{@link http://s7ips1.scene7.com/scene7/webservice/IpsApi.wsdl}</li>
 * <li>{@link https://ewus.nfz.gov.pl/ws-broker-server-ewus/services/ServiceBroker?wsdl}</li>
 * <li>{@link http://webservices.micros.com/ows/5.1/Security.wsdl}</li>
 * <li>{@link https://oivs.mvtrip.alabama.gov/service/XMLExchangeServiceCore.asmx?WSDL}</li>
 * <li>{@link http://91.93.143.3/bbmvoice/VoiceRecService.asmx?WSDL}</li>
 * <li>{@link http://92.45.22.83/CLZMobileWebService/MobileWebService.asmx?WSDL}</li>
 * <li>{@link http://87.106.12.100:9090/schemas/order-service.wsdl}</li>
 * <li>{@link http://destservices.touricoholidays.com/DestinationsService.svc?wsdl}</li>
 * <li>{@link http://partners.a2zinc.net/dataservices/public/exhibitorprovider.asmx?WSDL}</li>
 * <li>{@link http://sharepoint-wsdl.googlecode.com/svn/trunk/WSDL/dspsts.asmx.xml}, multiple header for a given operation</li>
 * <li>{@link http://eit.ebscohost.com/Services/SearchService.asmx?WSDL}</li>
 * <li>{@link http://drachenklasse.hosted-application.de/WebService.asmx?WSDL}</li>
 * <li>{@link https://api.cvent.com/soap/V200611.asmx?WSDL&debug=1}</li>
 * <li>{@link http://www.xignite.com/xIndices.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xCurrencies.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xInsider.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xCompensation.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xGlobalHistorical.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xRealTime.asmx?WSDL}</li>
 * <li>{@link https://adwords.google.com/api/adwords/cm/v201209/CampaignService?wsdl}</li>
 * <li>{@link https://americommerce.com/store/ws/AmeriCommerceDb.asmx?wsdl}</li>
 * <li>{@link http://www.relaiscolis.com/wsInfoRelaisTest/wsInfoRelais.asmx?WSDL}</li>
 * <li>{@link https://api.channeladvisor.com/ChannelAdvisorAPI/v3/MarketplaceAdService.asmx?WSDL}</li>
 * <li>{@link http://ws1.ems6.net/subscribers.asmx?WSDL}</li>
 * <li>{@link http://www.webxml.com.cn/WebServices/WeatherWebService.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xVWAP.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xScreener.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xChart.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xStatistics.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xHousing.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xExchanges.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xCalendar.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xIndexComponents.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xMetals.asmx?WSDL}</li>
 * <li>{@link http://globalrealtimeoptions.xignite.com/xglobalrealtimeoptions.asmx?WSDL}</li>
 * <li>{@link http://globaloptions.xignite.com/xglobaloptions.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xEnergy.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xFutures.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xMoneyMarkets.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xInterBanks.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xRates.asmx?WSDL}</li>
 * <li>{@link http://bondsrealtime.xignite.com/xBondsRealTime.asmx?WSDL}</li>
 * <li>{@link http://bonds.xignite.com/xBonds.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xFundHoldings.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xFundData.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xFunds.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xNews.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xOFAC.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xTranscripts.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xReleases.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xLogos.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xHoldings.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xEarningsCalendar.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xEstimates.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xEdgar.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xAnalysts.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xGlobalFundamentals.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xFundamentals.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xFinancials.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xHistorical.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xNASDAQLastSale.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xBATSLastSale.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xBATSRealTime.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xQuotes.asmx?WSDL}</li>
 * <li>{@link http://globalquotes.xignite.com/xglobalquotes.asmx?WSDL}</li>
 * <li>{@link http://globalrealtime.xignite.com/xglobalrealtime.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xWatchLists.asmx?WSDL}</li>
 * <li>{@link http://www.xignite.com/xSecurity.asmx?WSDL}</li>
 * <li>{@link http://globalbondmaster.xignite.com/xGlobalBondMaster.asmx?WSDL}</li>
 * <li>{@link http://globalmaster.xignite.com/xglobalmaster.asmx?WSDL}</li>
 * <li>{@link http://bondmaster.xignite.com/xBondMaster.asmx?WSDL}</li>
 * <li>{@link http://radiopilatusadmin.showare.sta.v-1.ch/WebServices/MemberDataServiceProvider.asmx?WSDL}</li>
 * <li>{@link http://mail.yahooapis.com/ws/mail/v1.1/wsdl}</li>
 * <li>{@link https://xhi.venere.com/xhi-1.0/services/OTA_ReadNotifReport.soap?wsdl}, multiple header for a given operation</li>
 * <li>{@link http://demo.braingroup.ch/financial-kernel-ws/b2c/1?wsdl}</li>
 * <li>{@link http://demo.braingroup.ch/financial-kernel-ws/tax/1?wsdl}</li>
 * <li>{@link http://commonwebservices.saralee-de.com/mcdb2g/Services.asmx?WSDL}</li>
 * <li>{@link http://staging.timatic.aero/timaticwebservices/timatic3.WSDL} sessionID for processLogin has a header with required="false"</li>
 * <li>{@link http://www.reservationfactory.com/wsdls/air_v21_0/Air.wsdl}</li>
 * <li>{@link http://unit4.detuinmachinecompany.com/wsdl.xml} WebID for operations is required with wsdl:required="true"</li>
 * <li>{@link http://www.martonhouse.net/Invensys/InvensysAPI.asmx?WSDL}</li>
 * <li>{@link http://yz.emsecure.net/automation/individual.asmx?WSDL}</li>
 * </ul>
 * </li>
 * <li>Similar struct name:
 * <ul>
 * <li>{@link http://voipnow2demo.4psa.com//soap2/schema/3.0.0/voipnowservice.wsdl} timeInterval/TimeInterval, recharge/Recharge</li>
 * </ul>
 * </li>
 * <li>Undefined parameter/return types by the \SoapClient but determined by the Generator class
 * <ul>
 * <li>{@link http://portaplusapi.icc-switch.com/soap12}</li>
 * </ul>
 * </li>
 * <li>Operations calls own obect parameter methods and inherited methods (php code and php doc must take account of the inheritance) :
 * <ul>
 * <li>{@link http://voipnow2demo.4psa.com//soap2/schema/3.0.0/voipnowservice.wsdl}, ex : Add, AddUser, AddServiceProvider</li>
 * <li>{@link http://www.reservationfactory.com/wsdls/air_v21_0/Air.wsdl}, ex : service</li>
 * </ul>
 * </li>
 * <li>Biggest Packages generated :
 * <ul>
 * <li>{@link https://raw.github.com/jkinred/psphere/master/psphere/wsdl/vimService.wsdl}</li>
 * <li>{@link https://americommerce.com/store/ws/AmeriCommerceDb.asmx?wsdl}</li>
 * <li>{@link http://www.ovh.com/soapi/soapi-dlw-1.54.wsdl}</li>
 * <li>{@link https://webservices.netsuite.com/wsdl/v2012_2_0/netsuite.wsdl}</li>
 * </ul>
 * </li>
 * <li>Web Service with multiple parameters of same type per operation, parameters are now named as the original parameter name
 * <ul>
 * <li>{@link http://demo.magentocommerce.com/api/v2_soap?wsdl=1}, ex: login operation</li>
 * </ul>
 * </li>
 * <li>Web Service with all parameter only detected with unknown parameter type and return per operation. These types must be retrieved from the WSDLs
 * <ul>
 * <li>{@link http://196.29.140.10:9091/services/MyBoardPack.Soap.svc?singleWsdl}</li>
 * </ul>
 * </li>
 * </ul>
 */
class Generator extends \SoapClient
{
    /**
     * Index where global values are stored in order to unset them once when it's necessary and to clean GLOBALS
     * @var string
     */
    const WSDL_TO_PHP_GENERATOR_GLOBAL_KEY = '__GeneratorGlobalKey__';
    /**
     * Index where audit values are stored in the global var
     * @var string
     */
    const WSDL_TO_PHP_GENERATOR_AUDIT_KEY = '__GeneratorAuditKey__';
    /**
     * Structs
     * @var StructContainer
     */
    private $structs;
    /**
     * Services
     * @var ServiceContainer
     */
    private $services;
    /**
     * Name of the package to use
     * @var string
     */
    private static $packageName;
    /**
     * Wsdl lists
     * @var WsdlContainer
     */
    private $wsdls;
    /**
     * Use intern global variable instead of using the PHP $GLOBALS variable
     * @var array
     */
    private static $globals;
    /**
     * @var GeneratorOptions
     */
    private $options;
    /**
     * Current generator instance
     * @var Generator
     */
    private static $instance;
    /**
     * Constructor
     * @uses \SoapClient::__construct()
     * @uses Generator::setStructs()
     * @uses Generator::setServices()
     * @uses Generator::setWsdls()
     * @uses Generator::addWsdl()
     * @param string $pathToWsdl WSDL url or path
     * @param string $login login to get access to WSDL
     * @param string $password password to get access to WSDL
     * @param array $wsdlOptions options to get access to WSDL
     * @return Generator
     */
    public function __construct($pathToWsdl, $login = false, $password = false, array $wsdlOptions = array())
    {
        $pathToWsdl = trim($pathToWsdl);
        /**
         * Options for WSDL
         */
        $options = $wsdlOptions;
        $options['trace'] = true;
        $options['exceptions'] = true;
        $options['cache_wsdl'] = WSDL_CACHE_NONE;
        $options['soap_version'] = SOAP_1_1;
        if (!empty($login) && !empty($password)) {
            $options['login'] = $login;
            $options['password'] = $password;
        }
        /**
         * Construct
         */
        try {
            parent::__construct($pathToWsdl, $options);
        } catch (\SoapFault $fault) {
            $options['soap_version'] = SOAP_1_2;
            try {
                parent::__construct($pathToWsdl, $options);
            } catch (\SoapFault $fault) {
                throw new \Exception(sprintf('Unable to load WSDL at "%s"!', $pathToWsdl), null, $fault);
            }
        }
        $this->setOptions(GeneratorOptions::instance());
        $this->setStructs(new StructContainer());
        $this->setServices(new ServiceContainer());
        $this->setWsdls(new WsdlContainer());
        $this->addWsdl($pathToWsdl);
    }
    /**
     * @param string options's file to parse
     * @return Generator
     */
    public static function instance($pathToWsdl = null, $login = false, $password = false, array $wsdlOptions = array())
    {
        if (!isset(self::$instance)) {
            if (empty($pathToWsdl)) {
                throw new \InvalidArgumentException('No Generator instance exists, you must provide the WSDL path to initiate the first instance!');
            }
            self::$instance = new static($pathToWsdl, $login, $password, $wsdlOptions);
        }
        return self::$instance;
    }
    /**
     * Generates all classes based on options
     * @uses Generator::setPackageName()
     * @uses Generator::getWsdl()
     * @uses Generator::getStructs()
     * @uses Generator::initStructs()
     * @uses Generator::getServices()
     * @uses Generator::initServices()
     * @uses Generator::loadWsdls()
     * @uses Generator::getOptionGenerateWsdlClassFile()
     * @uses Generator::generateWsdlClassFile()
     * @uses Generator::setOptionGenerateWsdlClassFile()
     * @uses Generator::generateStructsClasses()
     * @uses Generator::generateServicesClasses()
     * @uses Generator::generateClassMap()
     * @uses Generator::getOptionGenerateAutoloadFile()
     * @uses Generator::generateAutoloadFile()
     * @uses Generator::getOptionGenerateTutorialFile()
     * @uses Generator::generateTutorialFile()
     * @uses Generator::initGlobals()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @param string $packageName the string used to prefix all generate classes
     * @param string $rootDirectory path where classes should be generated
     * @param int $rootDirectoryRights system rights to apply on folder
     * @param bool $createRootDirectory create root directory if not exist
     * @return bool true|false depending on the well creation fot the root directory
     */
    public function generateClasses($packageName, $rootDirectory, $rootDirectoryRights = 0775, $createRootDirectory = true)
    {
        self::initGlobals();
        $wsdl = $this->getWsdl(0);
        $wsdlLocation = $wsdl !== null ? $wsdl->getName() : '';
        self::auditInit('generate_classes', $wsdlLocation);
        if (!empty($wsdlLocation)) {
            self::setPackageName($packageName);
            $rootDirectory = $rootDirectory . (substr($rootDirectory, -1) != '/' ? '/' : '');
            /**
             * Root directory
             */
            if (!is_dir($rootDirectory) && !$createRootDirectory) {
                throw new \InvalidArgumentException(sprintf('Unable to use dir "%s" as dir does not exists and its creation has been disabled', $rootDirectory));
            } elseif (!is_dir($rootDirectory) && $createRootDirectory) {
                mkdir($rootDirectory, $rootDirectoryRights);
            }
            /**
             * Begin process
             */
            if (is_dir($rootDirectory)) {
                /**
                 * Initialize elements
                 */
                $init = false;
                if ($this->getStructs()->count() === 0) {
                    $this->initStructs();
                } else {
                    $init = true;
                }
                if ($this->getServices()->count() === 0) {
                    $this->initServices();
                }
                if (!$init && $this->getWsdls()->count()) {
                    $this->loadWsdls($wsdlLocation);
                }
                /**
                 * Initialize specific elements when all wsdls are loaded
                 */
                $this->wsdlsLoaded();
                /**
                 * Generates Wsdl Class ?
                 */
                if ($this->getOptionGenerateWsdlClassFile() === true) {
                    $wsdlClassFile = $this->generateWsdlClassFile($rootDirectory);
                } else {
                    $wsdlClassFile = array();
                }
                if (!count($wsdlClassFile)) {
                    $this->setOptionGenerateWsdlClassFile(false);
                }
                /**
                 * Generates classes files
                 */
                $structsClassesFiles = $this->generateStructsClasses($rootDirectory, $rootDirectoryRights);
                $servicesClassesFiles = $this->generateServicesClasses($rootDirectory, $rootDirectoryRights);
                $classMapFile = $this->generateClassMap($rootDirectory);
                /**
                 * Generates autoload ?
                 */
                if ($this->getOptionGenerateAutoloadFile() === true) {
                    $this->generateAutoloadFile($rootDirectory, array_merge($wsdlClassFile, $structsClassesFiles, $servicesClassesFiles, $classMapFile));
                }
                /**
                 * Generates tutorial ?
                 */
                if ($this->getOptionGenerateTutorialFile() === true) {
                    $this->generateTutorialFile($rootDirectory, $servicesClassesFiles);
                }
                return self::audit('generate_classes', $wsdlLocation);
            } else {
                return !self::audit('generate_classes', $wsdlLocation);
            }
        }
        return !self::audit('generate_classes', $wsdlLocation);
    }
    /**
     * Initialize structs defined in WSDL :
     * - Get structs defined
     * - Parse each struct definition
     * - Analyze each struct paramaters
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @return bool true|false depending on the well types catching from the WSDL
     */
    private function initStructs()
    {
        self::auditInit('init_structs');
        $structsParser = new StructsParser($this);
        $structsParser->parse();
        return self::audit('init_structs');
    }
    /**
     * Generates structs classes based on structs collected
     * @uses Generator::getStructs()
     * @uses Generator::getDirectory()
     * @uses Generator::populateFile()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses AbstractModel::getName()
     * @uses AbstractModel::getModelByName()
     * @uses AbstractModel::getInheritance()
     * @uses AbstractModel::getCleanName()
     * @uses AbstractModel::getPackagedName()
     * @uses AbstractModel::getClassDeclaration()
     * @uses Struct::getIsStruct()
     * @param string $rootDirectory the directory
     * @param int $rootDirectoryRights the directory permissions
     */
    private function generateStructsClasses($rootDirectory, $rootDirectoryRights)
    {
        self::auditInit('generate_structs');
        $structs = $this->getStructs();
        $structsClassesFiles = array();
        if (count($structs)) {
            /**
             * Ordering structs in order to generate mother class first and to put them on top in the autoload file
             */
            $structsToGenerateDone = array();
            foreach ($structs as $struct) {
                if (!array_key_exists($struct->getName(), $structsToGenerateDone)) {
                    $structsToGenerateDone[$struct->getName()] = 0;
                }
                $model = AbstractModel::getModelByName($struct->getInheritance());
                while ($model && $model->getIsStruct()) {
                    if (!array_key_exists($model->getName(), $structsToGenerateDone)) {
                        $structsToGenerateDone[$model->getName()] = 1;
                    } else {
                        $structsToGenerateDone[$model->getName()]++;
                    }
                    $model = AbstractModel::getModelByName($model->getInheritance());
                }
            }
            /**
             * Order by priority desc
             */
            arsort($structsToGenerateDone);
            $structTmp = $structs;
            $structs = array();
            foreach (array_keys($structsToGenerateDone) as $structName)
                $structs[$structName] = $structTmp->getStructByName($structName);
            unset($structTmp, $structsToGenerateDone);
            foreach ($structs as $structName => $struct) {
                if (!$struct->getIsStruct()) {
                    continue;
                }
                $elementFolder = $this->getDirectory($rootDirectory, $rootDirectoryRights, $struct);
                array_push($structsClassesFiles, $structClassFileName = $elementFolder . $struct->getPackagedName() . '.php');
                /**
                 * Generates file
                 */
                self::populateFile($structClassFileName, $struct->getClassDeclaration());
            }
        }
        self::audit('generate_structs');
        return $structsClassesFiles;
    }
    /**
     * Initialize functions :
     * - Get structs defined
     * - Parse each struct definition
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @return bool true|false depending on the well functions catching from the WSDL
     */
    private function initServices()
    {
        self::auditInit('init_services');
        $servicesParser = new FunctionsParser($this);
        $servicesParser->parse();
        return !self::audit('init_services');
    }
    /**
     * Generates methods by class
     * @uses Generator::getServices()
     * @uses Generator::getDirectory()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses AbstractModel::getCleanName()
     * @uses AbstractModel::getPackagedName()
     * @uses AbstractModel::getClassDeclaration()
     * @param string $rootDirectory the directory
     * @param int $rootDirectoryRights the directory permissions
     * @return array the absolute paths to the generated files
     */
    private function generateServicesClasses($rootDirectory, $rootDirectoryRights)
    {
        self::auditInit('generate_services');
        $services = $this->getServices();
        $servicesClassesFiles = array();
        if (count($services)) {
            foreach ($services as $service) {
                $elementFolder = $this->getDirectory($rootDirectory, $rootDirectoryRights, $service);
                array_push($servicesClassesFiles, $serviceClassFileName = $elementFolder . $service->getPackagedName() . '.php');
                /**
                 * Generates file
                 */
                self::populateFile($serviceClassFileName, $service->getClassDeclaration());
            }
        }
        self::audit('generate_services');
        return $servicesClassesFiles;
    }
    /**
     * Populate the php file with the object and the declarations
     * @uses AbstractModel::cleanComment()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @param string $fileName the file name
     * @param array $declarations the lines of code and comments
     * @return void
     */
    private static function populateFile($fileName, array $declarations)
    {
        self::auditInit('populate');
        $content = array('<?php');
        $indentationString = "    ";
        $indentationLevel = 0;
        foreach ($declarations as $declaration) {
            if (is_array($declaration) && array_key_exists('comment', $declaration) && is_array($declaration['comment'])) {
                array_push($content, str_repeat($indentationString, $indentationLevel) . '/**');
                foreach ($declaration['comment'] as $subComment)
                    array_push($content, str_repeat($indentationString, $indentationLevel) . ' * ' . AbstractModel::cleanComment($subComment));
                array_push($content, str_repeat($indentationString, $indentationLevel) . ' */');
            } elseif (is_string($declaration)) {
                switch ($declaration) {
                    case '{':
                        array_push($content, str_repeat($indentationString, $indentationLevel) . $declaration);
                        $indentationLevel++;
                        break;
                    case '}':
                        $indentationLevel--;
                        array_push($content, str_repeat($indentationString, $indentationLevel) . $declaration);
                        break;
                    default:
                        array_push($content, str_repeat($indentationString, $indentationLevel) . $declaration);
                        break;
                }
            }
        }
        array_push($content, str_repeat($indentationString, $indentationLevel));
        file_put_contents($fileName, implode("\n", $content));
        self::audit('populate', $fileName);
    }
    /**
     * Generates classMap class
     * @uses Generator::getStructs()
     * @uses Generator::getPackageName()
     * @uses Generator::getOptionAddComments()
     * @uses Generator::populateFile()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses AbstractModel::getName()
     * @uses AbstractModel::getCleanName()
     * @param string $rootDirectory the directory
     * @return array the absolute path to the generated file
     */
    private function generateClassMap($rootDirectory)
    {
        self::auditInit('generate_classmap');
        $classMapDeclaration = array();
        /**
         * class map comments
         */
        $comments = array();
        array_push($comments, 'File for the class which returns the class map definition');
        array_push($comments, '@package ' . self::getPackageName());
        if (count($this->getOptionAddComments())) {
            foreach ($this->getOptionAddComments() as $tagName => $tagValue) {
                array_push($comments, "@$tagName $tagValue");
            }
        }
        array_push($classMapDeclaration, array('comment' => $comments));
        $comments = array();
        array_push($comments, 'Class which returns the class map definition by the static method ' . self::getPackageName() . 'ClassMap::classMap()');
        array_push($comments, '@package ' . self::getPackageName());
        if (count($this->getOptionAddComments())) {
            foreach ($this->getOptionAddComments() as $tagName => $tagValue) {
                array_push($comments, "@$tagName $tagValue");
            }
        }
        array_push($classMapDeclaration, array('comment' => $comments));
        /**
         * class map declaration
         */
        array_push($classMapDeclaration, 'class ' . self::getPackageName() . 'ClassMap');
        array_push($classMapDeclaration, '{');
        /**
         * classMap() method comments
         */
        $comments = array();
        array_push($comments, 'This method returns the array containing the mapping between WSDL structs and generated classes');
        array_push($comments, 'This array is sent to the \SoapClient when calling the WS');
        array_push($comments, '@return array');
        array_push($classMapDeclaration, array('comment' => $comments));
        /**
         * classMap() method body
         */
        array_push($classMapDeclaration, 'final public static function classMap()');
        array_push($classMapDeclaration, '{');
        $structs = $this->getStructs();
        $classesToMap = array();
        foreach ($structs as $struct) {
            if ($struct->getIsStruct()) {
                $classesToMap[$struct->getName()] = $struct->getPackagedName();
            }
        }
        ksort($classesToMap);
        array_push($classMapDeclaration, 'return ' . var_export($classesToMap, true) . ';');
        array_push($classMapDeclaration, '}');
        array_push($classMapDeclaration, '}');
        /**
         * Generates file
         */
        self::populateFile($filename = $rootDirectory . self::getPackageName() . 'ClassMap.php', $classMapDeclaration);
        unset($comments, $classMapDeclaration, $structs, $classesToMap);
        self::audit('generate_classmap');
        return array($filename);
    }
    /**
     * Generates autoload file for all classes.
     * The classes are loaded automatically in order of their dependency regarding their inheritance.
     * @uses Generator::getPackageName()
     * @uses Generator::getOptionAddComments()
     * @uses Generator::populateFile()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @param string $rootDirectory the directory
     * @param array $classesFiles the generated classes files
     * @return void
     */
    private function generateAutoloadFile($rootDirectory, array $classesFiles = array())
    {
        if (count($classesFiles)) {
            self::auditInit('generate_autoload');
            $autoloadDeclaration = array();
            $comments = array();
            array_push($comments, 'File to load generated classes once at once time');
            array_push($comments, '@package ' . self::getPackageName());
            if (count($this->getOptionAddComments())) {
                foreach ($this->getOptionAddComments() as $tagName => $tagValue) {
                    array_push($comments, "@$tagName $tagValue");
                }
            }
            array_push($autoloadDeclaration, array('comment' => $comments));
            $comments = array();
            array_push($comments, 'Includes for all generated classes files');
            if (count($this->getOptionAddComments())) {
                foreach ($this->getOptionAddComments() as $tagName => $tagValue) {
                    array_push($comments, "@$tagName $tagValue");
                }
            }
            array_push($autoloadDeclaration, array('comment' => $comments));
            foreach ($classesFiles as $classFile) {
                if (is_file($classFile)) {
                    array_push($autoloadDeclaration, 'require_once ' . str_replace($rootDirectory, 'dirname(__FILE__) . \'/', $classFile) . '\';');
                }
            }
            self::populateFile($rootDirectory . '/' . self::getPackageName() . 'Autoload.php', $autoloadDeclaration);
            unset($autoloadDeclaration, $comments);
            self::audit('generate_autoload');
        }
    }
    /**
     * Generates Wsdl Class file
     * @uses Generator::getPackageName()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses AbstractModel::cleanComment()
     * @param string $rootDirectory the directory
     * @return array the absolute path to the generated file
     */
    private function generateWsdlClassFile($rootDirectory)
    {
        $pathToWsdlClassTemplate = dirname(__FILE__) . '/../Resources/templates/Default/Class.php';
        if (is_file($pathToWsdlClassTemplate)) {
            self::auditInit('generate_wsdlclass');
            /**
             * Adds additional PHP doc block tags if needed to the two main PHP doc block
             */
            if (count($this->getOptionAddComments())) {
                $file = file($pathToWsdlClassTemplate);
                $content = array();
                $counter = 2;
                foreach ($file as $line) {
                    if (empty($line)) {
                        continue;
                    }
                    if (strpos($line, ' */') === 0 && $counter) {
                        foreach ($this->getOptionAddComments() as $tagName => $tagValue) {
                            array_push($content, " * @$tagName $tagValue\n");
                        }
                        $counter--;
                    }
                    array_push($content, $line);
                }
                $content = implode('', $content);
            } else {
                $content = file_get_contents($pathToWsdlClassTemplate);
            }
            $metaInformation = '';
            foreach ($this->getWsdls() as $wsdl) {
                foreach ($wsdl->getMeta() as $metaName => $metaValue) {
                    $metaValueCleaned = AbstractModel::cleanComment($metaValue);
                    if ($metaValueCleaned === '') {
                        continue;
                    }
                    $metaInformation .= (!empty($metaInformation) ? "\n * " : '') . ucfirst($metaName) . " : $metaValueCleaned";
                }
            }
            $content = str_replace(array(
                'packageName',
                'PackageName',
                'meta_informations',
                "'wsdl_url_value'"
            ), array(
                lcfirst(self::getPackageName(false)),
                self::getPackageName(),
                $metaInformation,
                var_export(self::getWsdl(0)->getName(), true)
            ), $content);
            file_put_contents($rootDirectory . self::getPackageName() . 'WsdlClass.php', $content);
            self::audit('generate_wsdlclass');
            return array($rootDirectory . self::getPackageName() . 'WsdlClass.php');
        } else {
            return array();
        }
    }
    /**
     * Generates tutorial file
     * @uses Generator::getOptionGenerateAutoloadFile()
     * @uses Generator::getWsdls()
     * @uses Generator::getWsdl()
     * @uses Generator::getPackageName()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses ReflectionClass::getMethods()
     * @uses ReflectionMethod::getName()
     * @uses ReflectionMethod::getParameters()
     * @param string $rootDirectory the direcoty
     * @param array $methodsClassesFiles the generated class files
     * @return bool true|false
     */
    private function generateTutorialFile($rootDirectory, array $methodsClassesFiles = array())
    {
        if ($this->getOptionGenerateAutoloadFile() === true) {
            $pathToTutorialTemplate = dirname(__FILE__) . '/../Resources/templates/Default/sample.php';
            if (!is_file($pathToTutorialTemplate)) {
                throw new \InvalidArgumentException(sprintf('Unable to find tutorial template at "%s"', $pathToTutorialTemplate));
            }
            if (!is_file($rootDirectory . '/' . self::getPackageName() . 'Autoload.php')) {
                throw new \InvalidArgumentException(sprintf('Unable to find autoload file at "%s"', $rootDirectory . '/' . self::getPackageName() . 'Autoload.php'));
            } else {
                require_once $rootDirectory . '/' . self::getPackageName() . 'Autoload.php';
            }
            if (class_exists('ReflectionClass') && count($methodsClassesFiles)) {
                self::auditInit('generate_tutorial');
                $content = '';
                foreach ($methodsClassesFiles as $classFilePath) {
                    $pathinfo = pathinfo($classFilePath);
                    $className = str_replace('.' . $pathinfo['extension'], '', $pathinfo['filename']);
                    if (class_exists($className)) {
                        $r = new \ReflectionClass($className);
                        $methods = $r->getMethods();
                        $classMethods = array();
                        foreach ($methods as $method) {
                            if ($method->class === $className && !in_array($method->getName(), array('__toString', '__construct', 'getResult'))) {
                                array_push($classMethods, $method);
                            }
                        }
                        if (count($classMethods)) {
                            $classNameVar = lcfirst($className);
                            $content .= "\n\n/**" . str_repeat('*', strlen("Example for $className")) . "\n * Example for $className\n */";
                            $content .= "\n\$$classNameVar = new $className();";
                            foreach ($classMethods as $classMethod) {
                                $content .= "\n// sample call for $className::" . $classMethod->getName() . '()';
                                $methodDoComment = $classMethod->getDocComment();
                                $methodParameters = $classMethod->getParameters();
                                $methodParametersCount = count($methodParameters);
                                $isSetSoapHeaderMethod = (strpos($classMethod->getName(), 'setSoapHeader') === 0 && strlen($classMethod->getName()) > strlen('setSoapHeader'));
                                $end = $isSetSoapHeaderMethod ? 1 : $methodParametersCount;
                                $parameters = array();
                                for ($i = 0; $i < $end; $i++) {
                                    $methodParameter = $methodParameters[$i];
                                    /**
                                     * Remove first _
                                     */
                                    $methodParameterName = substr($methodParameter->getName(), 1);
                                    /**
                                     * Retrieve parameter type based on the method doc comment
                                     */
                                    $matches = array();
                                    preg_match('/\@param\s(.*)\s\$' . $methodParameterName . '\n/', $methodDoComment, $matches);
                                    $methodParameterType = (array_key_exists(1, $matches) && class_exists($matches[1])) ? ucfirst($matches[1]) : null;
                                    array_push($parameters, !empty($methodParameterType) ? "new $methodParameterType(/*** update parameters list ***/)" : "\$$methodParameterName");
                                }
                                /**
                                 * setSoapHeader call
                                 */
                                if ($isSetSoapHeaderMethod) {
                                    $content .= " in order to initialize required SoapHeader";
                                    $content .= "\n\$$classNameVar->" . $classMethod->getName() . '(' . implode(', ', $parameters) . ');';
                                } else {
                                    /**
                                     * Operation call
                                     */
                                    $content .= "\nif(\$$classNameVar->" . $classMethod->getName() . '(' . implode(', ', $parameters) . '))';
                                    $content .= "\n    " . 'print_r($' . $classNameVar . '->getResult());';
                                    $content .= "\nelse";
                                    $content .= "\n    print_r($" . $classNameVar . "->getLastError());";
                                }
                            }
                        }
                    }
                }
                if (!empty($content)) {
                    /**
                     * Adds additional PHP doc block tags if needed to the one main PHP doc block
                     */
                    if (count($this->getOptionAddComments())) {
                        $file = file($pathToTutorialTemplate);
                        $fileContent = array();
                        $counter = 1;
                        foreach ($file as $line) {
                            if (empty($line)) {
                                continue;
                            }
                            if (strpos($line, ' */') === 0 && $counter) {
                                foreach ($this->getOptionAddComments() as $tagName => $tagValue) {
                                    array_push($fileContent, " * @$tagName $tagValue\n");
                                }
                                $counter--;
                            }
                            array_push($fileContent, $line);
                        }
                        $fileContent = implode('', $fileContent);
                    } else {
                        $fileContent = file_get_contents($pathToTutorialTemplate);
                    }
                    $fileContent = str_replace(array(
                        'packageName',
                        'PackageName',
                        'PACKAGENAME',
                        'WSDL_PATH',
                        '$content;'
                    ), array(
                        lcfirst(self::getPackageName()),
                        ucfirst(self::getPackageName()),
                        strtoupper(self::getPackageName()),
                        var_export($this->getWsdl(0)->getName(), true),
                        $content
                    ), $fileContent);
                    file_put_contents($rootDirectory . 'sample.php', $fileContent);
                }
                self::audit('generate_tutorial');
                return true;
            } elseif (!class_exists('ReflectionClass')) {
                throw new \InvalidArgumentException("Generator::generateTutorialFile() needs ReflectionClass, see http://fr2.php.net/manual/fr/class.reflectionclass.php");
            }
        }
    }
    /**
     * Gets the struct by its name
     * @uses Generator::getStructs()
     * @param string $structName the original struct name
     * @return Struct|null
     */
    public function getStruct($structName)
    {
        return $this->structs->getStructByName($structName);
    }
    /**
     * Adds an info to the struct
     * @uses Generator::getStruct()
     * @uses AbstractModel::addMeta()
     * @param string $structName the original struct name
     * @param string $structInfoName the struct info name
     * @param mixed $structInfoValue the struct info value
     * @return void
     */
    public function addStructMeta($structName, $structInfoName, $structInfoValue)
    {
        if ($this->getStruct($structName) !== null) {
            $this->getStruct($structName)->addMeta($structInfoName, $structInfoValue);
        }
    }
    /**
     * Sets struct inheritance value
     * @uses Generator::getStruct()
     * @uses AbstractModel::setInheritance()
     * @param string the original struct name
     * @param string the struct inheritance name
     * @return void
     */
    private function setStructInheritance($structName, $inherits)
    {
        if ($this->getStruct($structName) !== null) {
            $this->getStruct($structName)->setInheritance($inherits);
        }
    }
    /**
     * Adds struct documentation info
     * @uses Generator::getStruct()
     * @uses AbstractModel::setDocumentation()
     * @param string $structName the original struct name
     * @param string $documentation the struct documentation
     * @return void
     */
    private function setStructDocumentation($structName, $documentation)
    {
        if ($this->getStruct($structName) !== null) {
            $this->getStruct($structName)->setDocumentation($documentation);
        }
    }
    /**
     * Sets the struct as a restriction, which means it contains the enumeration values
     * @uses Generator::getStruct()
     * @uses Struct::setIsRestriction()
     * @param string $structName the original struct name
     * @return void
     */
    private function setStructIsRestriction($structName)
    {
        if ($this->getStruct($structName) !== null) {
            $this->getStruct($structName)->setIsRestriction(true);
        }
    }
    /**
     * Sets the struct as a srtuct, which means it has to be generated as a class
     * @uses Generator::getStruct()
     * @uses Struct::setIsStruct()
     * @param string $structName the original struct name
     * @return void
     */
    private function setStructIsStruct($structName)
    {
        if ($this->getStruct($structName) !== null) {
            $this->getStruct($structName)->setIsStruct(true);
        }
    }
    /**
     * Gets the struct by its name
     * @uses Generator::getStruct()
     * @uses Struct::getAttribute()
     * @param string $structName the original struct name
     * @param string $attributeName the attribute name
     * @return StructAttribute|null
     */
    public function getStructAttribute($structName, $attributeName)
    {
        return $this->getStruct($structName) !== null ? $this->getStruct($structName)->getAttribute($attributeName) : null;
    }
    /**
     * Adds an info to the struct attribute
     * @uses Generator::getStructAttribute()
     * @uses AbstractModel::addMeta()
     * @param string $structName the original struct name
     * @param string $attributeName the attribute name
     * @param string $attributeInfoName the attribute info name
     * @param mixed $attributeInfoValue the attribute info value
     * @return void
     */
    private function addStructAttributeMeta($structName, $attributeName, $attributeInfoName, $attributeInfoValue)
    {
        if ($this->getStructAttribute($structName, $attributeName) !== null) {
            $this->getStructAttribute($structName, $attributeName)->addMeta($attributeInfoName, $attributeInfoValue);
        }
    }
    /**
     * Adds struct documentation info
     * @uses Generator::getStructAttribute()
     * @uses AbstractModel::setDocumentation()
     * @param string $structName the original struct name
     * @param string $attributeName the attribute name
     * @param string $documentation the attribute documentation
     * @return void
     */
    private function setStructAttributeDocumentation($structName, $attributeName, $documentation)
    {
        if ($this->getStructAttribute($structName, $attributeName) !== null) {
            $this->getStructAttribute($structName, $attributeName)->setDocumentation($documentation);
        }
    }
    /**
     * Gets the struct value by its name
     * @uses Generator::getStruct()
     * @uses Struct::getValue()
     * @param string $structName the original struct name
     * @param string $valueName the value name
     * @return StructValue|null
     */
    public function getStructValue($structName, $valueName)
    {
        return $this->getStruct($structName) !== null ? $this->getStruct($structName)->getValue($valueName) : null;
    }
    /**
     * Adds value to restriction struct
     * @uses Generator::getStruct()
     * @uses Generator::setStructIsRestriction()
     * @uses Generator::setStructIsStruct()
     * @uses Struct::addValue()
     * @param string $structName the original struct name
     * @param mixed $value the value
     * @return void
     */
    public function addRestrictionValue($structName, $value)
    {
        if ($this->getStruct($structName) !== null) {
            $this->setStructIsRestriction($structName);
            $this->setStructIsStruct($structName);
            $this->getStruct($structName)->addValue($value);
        }
    }
    /**
     * Adds struct value documentation info
     * @uses Generator::getStructValue()
     * @uses AbstractModel::setDocumentation()
     * @param string $structName the original struct name
     * @param string $valueName the value name
     * @param string $documentation the value documentation
     * @return void
     */
    private function setStructValueDocumentation($structName, $valueName, $documentation)
    {
        if ($this->getStructValue($structName, $valueName) !== null) {
            $this->getStructValue($structName, $valueName)->setDocumentation($documentation);
        }
    }
    /**
     * Gets a service by its name
     * @param string $serviceName the service name
     * @return Service|null
     */
    public function getService($serviceName)
    {
        return $this->services->getServiceByName($serviceName);
    }
    /**
     * Returns the method
     * @uses Generator::getServiceName()
     * @uses Generator::getService()
     * @uses Service::getMethod()
     * @param string $methodName the original function name
     * @param mixed $methodParameter the original function paramter
     * @return Method|null
     */
    public function getServiceMethod($methodName)
    {
        return $this->getService($this->getServiceName($methodName)) !== null ? $this->getService($this->getServiceName($methodName))->getMethod($methodName) : null;
    }
    /**
     * Sets the service function documentation
     * @uses Generator::getServiceMethod()
     * @uses AbstractModel::setDocumentation()
     * @param string $methodName the service name
     * @param string $documentation the documentation
     * @return void
     */
    private function setServiceFunctionDocumentation($methodName, $documentation)
    {
        if ($this->getServiceMethod($methodName) !== null) {
            $this->getServiceMethod($methodName)->setDocumentation($documentation);
        }
    }
    /**
     * Adds the service function a meta information
     * @uses Generator::getServiceMethod()
     * @uses AbstractModel::addMeta()
     * @param string $methodName the service name
     * @param string $methodInfoName the method name
     * @param string $methodInfoValue the method info value
     * @return void
     */
    private function addServiceFunctionMeta($methodName, $methodInfoName, $methodInfoValue)
    {
        if ($this->getServiceMethod($methodName) !== null) {
            $this->getServiceMethod($methodName)->addMeta($methodInfoName, $methodInfoValue);
        }
    }
    /**
     * Sets the optionCategory value
     * @return string
     */
    public function getOptionCategory()
    {
        return $this->options->getCategory();
    }
    /**
     * Sets the optionCategory value
     * @param string
     * @return GeneratorOptions
     */
    public function setOptionCategory($category)
    {
        return $this->options->setCategory($category);
    }
    /**
     * Sets the optionSubCategory value
     * @return string
     */
    public function getOptionSubCategory()
    {
        return $this->options->getSubCategory();
    }
    /**
     * Sets the optionSubCategory value
     * @param string
     * @return GeneratorOptions
     */
    public function setOptionSubCategory($subCategory)
    {
        return $this->options->setSubCategory($subCategory);
    }
    /**
     * Sets the optionGatherMethods value
     * @return string
     */
    public function getOptionGatherMethods()
    {
        return $this->options->getGatherMethods();
    }
    /**
     * Sets the optionGatherMethods value
     * @param string
     * @return GeneratorOptions
     */
    public function setOptionGatherMethods($gatherMethods)
    {
        return $this->options->setGatherMethods($gatherMethods);
    }
    /**
     * Gets the optionSendArrayAsParameter value
     * @return bool
     */
    public function getOptionSendArrayAsParameter()
    {
        return $this->options->getSendArrayAsParameter();
    }
    /**
     * Sets the optionSendArrayAsParameter value
     * @apram bool
     * @return GeneratorOptions
     */
    public function setOptionSendArrayAsParameter($sendArrayAsParameter)
    {
        return $this->options->setSendArrayAsParameter($sendArrayAsParameter);
    }
    /**
     * Gets the optionGenerateAutoloadFile value
     * @return bool
     */
    public function getOptionGenerateAutoloadFile()
    {
        return $this->options->getGenerateAutoloadFile();
    }
    /**
     * Sts the optionGenerateAutoloadFile value
     * @param bool
     * @return GeneratorOptions
     */
    public function setOptionGenerateAutoloadFile($generateAutoloadFile)
    {
        return $this->options->setGenerateAutoloadFile($generateAutoloadFile);
    }
    /**
     * Gets the optionGenerateWsdlClassFile value
     * @return bool
     */
    public function getOptionGenerateWsdlClassFile()
    {
        return $this->options->getGenerateWsdlClass();
    }
    /**
     * @param bool $optionGenerateWsdlClassFile
     * @return GeneratorOptions
     */
    public function setOptionGenerateWsdlClassFile($optionGenerateWsdlClassFile)
    {
        return $this->options->setGenerateWsdlClass($optionGenerateWsdlClassFile);
    }
    /**
     * Gets the optionResponseAsWsdlObject value
     * @return bool
     */
    public function getOptionResponseAsWsdlObject()
    {
        return $this->options->getGetResponseAsWsdlObject();
    }
    /**
     * Sets the optionResponseAsWsdlObject value
     * @param bool
     * @return GeneratorOptions
     */
    public function setOptionGetResponseAsWsdlObject($responseAsWsdlObject)
    {
        return $this->options->setGetResponseAsWsdlObject($responseAsWsdlObject);
    }
    /**
     * Gets the optionResponseAsWsdlObject value
     * @return bool
     */
    public function getOptionSendParametersAsArray()
    {
        return $this->options->getSendParametersAsArray();
    }
    /**
     * Sets the optionResponseAsWsdlObject value
     * @param bool
     * @return GeneratorOptions
     */
    public function setOptionSendParametersAsArray($sendParametersAsArray)
    {
        return $this->options->setSendParametersAsArray($sendParametersAsArray);
    }
    /**
     * Gets the optionInheritsClassIdentifier value
     * @return string
     */
    public function getOptionInheritsClassIdentifier()
    {
        return $this->options->getInheritsFromIdentifier();
    }
    /**
     * Sets the optionInheritsClassIdentifier value
     * @param string
     * @return GeneratorOptions
     */
    public function setOptionInheritsClassIdentifier($inheritsFromIdentifier)
    {
        return $this->options->setInheritsFromIdentifier($inheritsFromIdentifier);
    }
    /**
     * Gets the optionGenericConstantsNames value
     * @return bool
     */
    public function getOptionGenericConstantsNames()
    {
        return $this->options->getGenericConstantsName();
    }
    /**
     * Sets the optionGenericConstantsNames value
     * @param bool
     * @return GeneratorOptions
     */
    public function setOptionGenericConstantsNames($genericConstantsNames)
    {
        return $this->options->setGenericConstantsName($genericConstantsNames);
    }
    /**
     * Gets the optionGenerateTutorialFile value
     * @return bool
     */
    public function getOptionGenerateTutorialFile()
    {
        return $this->options->getGenerateTutorialFile();
    }
    /**
     * Sets the optionGenerateTutorialFile value
     * @param bool
     * @return GeneratorOptions
     */
    public function setOptionGenerateTutorialFile($generateTutorialFile)
    {
        return $this->options->setGenerateTutorialFile($generateTutorialFile);
    }
    /**
     * Gets the optionAddComments value
     * @return array
     */
    public function getOptionAddComments()
    {
        return $this->options->getAddComments();
    }
    /**
     * Sets the optionAddComments value
     * @param array
     * @return GeneratorOptions
     */
    public function setOptionAddComments($addComments)
    {
        return $this->options->setAddComments($addComments);
    }
    /**
     * Gets the package name
     * @param bool $ucFirst ucfirst package name or not
     * @return string
     */
    public static function getPackageName($ucFirst = true)
    {
        return $ucFirst ? ucfirst(self::$packageName) : self::$packageName;
    }
    /**
     * Sets the package name
     * @param string $packageName
     * @return string
     */
    private static function setPackageName($packageName)
    {
        return (self::$packageName = $packageName);
    }
    /**
     * Gets the WSDLs
     * @return WsdlContainer
     */
    public function getWsdls()
    {
        return $this->wsdls;
    }
    /**
     * Gets the WSDL at the index
     * @param int $index
     * @return Wsdl
     */
    public function getWsdl($index)
    {
        return $this->getWsdls()->offsetExists($index) ? $this->getWsdls()->offsetGet($index) : null;
    }
    /**
     * Sets the WSDLs
     * @param WsdlContainer $wsdlContainer
     * @return Generator
     */
    public function setWsdls(WsdlContainer $wsdlContainer)
    {
        $this->wsdls = $wsdlContainer;
        return $this;
    }
    /**
     * Adds Wsdl location
     * @param string $wsdlLocation
     */
    public function addWsdl($wsdlLocation)
    {
        if (!empty($wsdlLocation) && $this->wsdls->getWsdlByName($wsdlLocation) === null) {
            $this->wsdls->add(new Wsdl($wsdlLocation, $this->getUrlContent($wsdlLocation)));
        }
        return $this;
    }
    /**
     * Adds Wsdl location meta information
     * @uses Generator::getWsdl()
     * @param string $metaName meta name
     * @param mixed $metaValue meta value
     * @return string
     */
    public function addWsdlMeta($wsdlLocation, $metaName, $metaValue)
    {
        if ($this->getWsdls()->getWsdlByName($wsdlLocation) !== null) {
            $this->getWsdls()->getWsdlByName($wsdlLocation)->addMeta($metaName, $metaValue);
        }
        return $this;
    }
    /**
     * Methods to load WSDL from current WSDL when current WSDL imports other WSDL
     * @uses Generator::manageWsdlLocation()
     * @uses Generator::manageWsdlNode()
     * @param string $wsdlLocation wsdl location to load
     * @param \DOMNode $domNode \DOMNode to browse
     * @param string $fromWsdlLocation wsdl location where the current $domNode or $wsdlLocation is from
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    private function loadWsdls($wsdlLocation = '', $domNode = null, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        /**
         * Not empty location
         */
        if (!empty($wsdlLocation)) {
            $this->manageWsdlLocation($wsdlLocation, $domNode, $fromWsdlLocation, $nodeNameMatch);
        } elseif ($domNode instanceof \DOMElement) {
            /**
             * New node to browse
             */
            $this->manageWsdlNode($wsdlLocation, $domNode, $fromWsdlLocation, $nodeNameMatch);
        }
    }
    /**
     * Method called when wsdls are loaded and all the structs/operations are loaded
     * Then we can manage some features which can be dependent of all the wsdls linked to the main WSDL
     * @uses Generator::getWsdls()
     * @uses Generator::manageWsdlLocation()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @return void
     */
    protected function wsdlsLoaded()
    {
        self::auditInit(__METHOD__);
        if ($this->getWsdls()->count() > 0) {
            $tags = array();
            /**
             * Retrieve headers informations
             */
            array_push($tags, 'header');
            /**
             * Retrieve list informations so inheritance and types are fully retrieved for the next step
             */
            array_push($tags, 'list');
            /**
             * Retrieve union informations so inheritance and types are fully retrieved for the next step
             */
            array_push($tags, 'union');
            /**
             * Retrieve attribute informations so inheritence and type are fully retrieved
             */
            array_push($tags, 'attribute');
            /**
             * Retrieve operation message types in order to fully determine themselves
             */
            array_push($tags, 'input');
            /**
             * Retrieve operation message types in order to fully determine themselves
             */
            array_push($tags, 'output');
            foreach ($tags as $tagName) {
                foreach ($this->getWsdls() as $wsdl) {
                    $this->manageWsdlLocation($wsdl->getName(), null, '', $tagName);
                }
            }
        }
        self::audit(__METHOD__);
    }
    /**
     * Default manage method for a location
     * @uses Generator::wsdlLocationToDomDocument()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMNodeList::item()
     * @uses \DOMNode::hasChildNodes()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlLocation($wsdlLocation, $domNode, $fromWsdlLocation, $nodeNameMatch = null)
    {
        self::auditInit(__METHOD__);
        $domDocument = self::wsdlLocationToDomDocument($wsdlLocation);
        if ($domDocument && $domDocument->hasChildNodes()) {
            $childNodes = $domDocument->childNodes;
            $childNodesLength = $childNodes->length;
            /**
             * Finds first valid element (avoid comments for example)
             */
            for ($i = 0; $i < $childNodesLength; $i++) {
                if ($childNodes->item($i) instanceof \DOMElement) {
                    $this->loadWsdls('', $childNodes->item($i), $wsdlLocation, $nodeNameMatch);
                    break;
                }
            }
        }
        self::audit(__METHOD__);
    }
    /**
     * Default manage method for a node
     * @uses Generator::manageWsdlNodeImport()
     * @uses Generator::manageWsdlNodeRestriction()
     * @uses Generator::manageWsdlNodeElement()
     * @uses Generator::manageWsdlNodeDocumentation()
     * @uses Generator::manageWsdlNodeExtension()
     * @uses Generator::manageWsdlNodeUndefined()
     * @uses Generator::loadWsdls()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMNode::item()
     * @uses \DOMNode::hasChildNodes()
     * @uses \DOMNode::hasAttributes()
     * @uses \DOMElement::hasAttribute()
     * @uses \DOMElement::getAttribute()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNode($wsdlLocation = '', $domNode = null, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        if (empty($nodeNameMatch)) {
            if (stripos($domNode->nodeName, 'import') !== false || stripos($domNode->nodeName, 'include') !== false) {
                /**
                 * Current node is type of "import" or "include"
                 */
                $this->manageWsdlNodeImport($wsdlLocation, $domNode, $fromWsdlLocation);
            } elseif (stripos($domNode->nodeName, 'restriction') !== false) {
                /**
                 * Restriction
                 */
                $this->manageWsdlNodeRestriction($wsdlLocation, $domNode, $fromWsdlLocation);
            } elseif (stripos($domNode->nodeName, 'enumeration') !== false) {
                /**
                 * Enumeration value
                 */
                $this->manageWsdlNodeEnumeration($wsdlLocation, $domNode, $fromWsdlLocation);
            } elseif ($domNode->hasAttribute('name') && $domNode->getAttribute('name') != '' && $domNode->hasAttribute('type') && $domNode->getAttribute('type') != '') {
                /**
                 * Element's, part of a struct called attribute
                 */
                $this->manageWsdlNodeAttribute($wsdlLocation, $domNode, $fromWsdlLocation);
            } elseif (stripos($domNode->nodeName, 'element') !== false || stripos($domNode->nodeName, 'complextype') !== false) {
                /**
                 * Element
                 */
                $this->manageWsdlNodeElement($wsdlLocation, $domNode, $fromWsdlLocation);
            } elseif (stripos($domNode->nodeName, 'documentation') !== false && !empty($domNode->nodeValue)) {
                /**
                 * Documentation's
                 */
                $this->manageWsdlNodeDocumentation($wsdlLocation, $domNode, $fromWsdlLocation);
            } elseif (stripos($domNode->nodeName, 'extension') !== false && $domNode->hasAttribute('base') && $domNode->getAttribute('base') != '') {
                /**
                 * Extension of struct
                 */
                $this->manageWsdlNodeExtension($wsdlLocation, $domNode, $fromWsdlLocation);
            } else {
                /**
                 * Undefined node
                 */
                $this->manageWsdlNodeUndefined($wsdlLocation, $domNode, $fromWsdlLocation);
            }
        } elseif (is_string($nodeNameMatch) && stripos($domNode->nodeName, $nodeNameMatch) !== false) {
            $manageWsdlNodeMethodName = 'manageWsdlNode' . ucfirst($nodeNameMatch);
            if (method_exists($this, $manageWsdlNodeMethodName)) {
                self::auditInit('managewsdlnode_' . $nodeNameMatch, !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
                $this->$manageWsdlNodeMethodName($wsdlLocation, $domNode, $fromWsdlLocation, $nodeNameMatch);
                self::audit('managewsdlnode_' . $nodeNameMatch, !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
            }
        }
        /**
         * other child nodes
         */
        if ($domNode->hasChildNodes()) {
            $childNodes = $domNode->childNodes;
            $childNodesLength = $childNodes->length;
            for ($i = 0; $i < $childNodesLength; $i++) {
                if ($childNodes->item($i)) {
                    $this->loadWsdls('', $childNodes->item($i), !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation, $nodeNameMatch);
                }
            }
        }
    }
    /**
     * Undefined node manage method
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @return boolean
     */
    protected function manageWsdlNodeUndefined($wsdlLocation = '', $domNode = null, $fromWsdlLocation = '')
    {
        return true;
    }
    /**
     * Manages shema import method
     * @uses Generator::addWsdl()
     * @uses Generator::loadWsdls()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMElement::hasAttribute()
     * @uses \DOMElement::getAttribute()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @return void
     */
    protected function manageWsdlNodeImport($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '')
    {
        self::auditInit('managewsdlnode_import', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        $location = '';
        if ($domNode->hasAttribute('location')) {
            $location = $domNode->getAttribute('location');
        } elseif ($domNode->hasAttribute('schemaLocation')) {
            $location = $domNode->getAttribute('schemaLocation');
        } elseif ($domNode->hasAttribute('schemalocation')) {
            $location = $domNode->getAttribute('schemalocation');
        }
        if (substr($location, 0, 2) == './') {
            $location = substr($location, 2);
        }
        /**
         * Define valid location
         */
        $locations = array();
        if (!empty($location) && strpos($location, 'http://') === false && strpos($location, 'https://') === false && (!empty($wsdlLocation) || !empty($fromWsdlLocation))) {
            $locationsToParse = array();
            array_push($locationsToParse, !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
            foreach ($locationsToParse as $locationToParse) {
                $fileParts = pathinfo($locationToParse);
                $fileBasename = (is_array($fileParts) && array_key_exists('basename', $fileParts)) ? $fileParts['basename'] : '';
                $parts = parse_url(str_replace($fileBasename, '', $locationToParse));
                $scheme = (is_array($parts) && array_key_exists('scheme', $parts)) ? $parts['scheme'] : '';
                $host = (is_array($parts) && array_key_exists('host', $parts)) ? $parts['host'] : '';
                $path = (is_array($parts) && array_key_exists('path', $parts)) ? $parts['path'] : '';
                $path = str_replace($fileBasename, '', $path);
                $cleanLocation = array();
                $locationToParseParts = explode('/', $location);
                $pathParts = explode('/', $path);
                foreach ($locationToParseParts as $locationPart) {
                    if ($locationPart == '..') {
                        $pathParts = count($pathParts) >= 2 ? array_slice($pathParts, 0, count($pathParts) - 2) : $pathParts;
                    } else {
                        array_push($cleanLocation, $locationPart);
                    }
                }
                $port = (is_array($parts) && array_key_exists('port', $parts)) ? $parts['port'] : '';
                /**
                 * Remote file
                 */
                if (!empty($scheme) && !empty($host)) {
                    array_push($locations, str_replace('urn', 'http', $scheme) . '://' . $host . (!empty($port) ? ':' . $port : '') . (count($pathParts) ? str_replace('//', '/', '/' . implode('/', $pathParts) . '/') : '/') . implode('/', $cleanLocation));
                } elseif (empty($scheme) && empty($host) && count($pathParts)) {
                    /**
                     * Local file
                     */
                    $localPath = str_replace('//', '/', implode('/', $pathParts) . '/');
                    $localFile = $localPath . implode('/', $cleanLocation);
                    if (is_file($localFile)) {
                        array_push($locations, $localFile);
                    }
                }
            }
        } elseif (!empty($location)) {
            array_push($locations, $location);
        }
        /**
         * New WSDL
         */
        foreach ($locations as $location) {
            if (!empty($location) && !array_key_exists($location, $this->getWsdls())) {
                /**
                 * Save Wsdl location
                 */
                $this->addWsdl($location);
                /**
                 * Load Wsdl
                 */
                $this->loadWsdls($location, null, $wsdlLocation);
            }
        }
        self::audit('managewsdlnode_import', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
    }
    /**
     * Manages restriction method
     * @uses Generator::findSuitableParent()
     * @uses Generator::setStructInheritance()
     * @uses Generator::addVirtualStruct()
     * @uses Generator::addStructMeta()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMNodeList::item()
     * @uses \DOMNode::hasChildNodes()
     * @uses \DOMNode::hasAttributes()
     * @uses \DOMElement::hasAttribute()
     * @uses \DOMElement::getAttribute()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @return void
     */
    protected function manageWsdlNodeRestriction($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '')
    {
        self::auditInit('managewsdlnode_restriction', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        /**
         * Finds parent node of this enumeration node
         */
        $parentNode = self::findSuitableParent($domNode);
        if ($parentNode) {
            /**
             * Inheritance detection
             */
            if ($domNode->hasAttribute('base')) {
                $type = explode(':', $domNode->getAttribute('base'));
                if (count($type) && !empty($type[count($type) - 1]) && $type[count($type) - 1] != $parentNode->getAttribute('name')) {
                    $this->setStructInheritance($parentNode->getAttribute('name'), $type[count($type) - 1]);
                }
            }
            /**
             * Meta informations about struct
             */
            if ($domNode->hasChildNodes()) {
                $childNodes = $domNode->childNodes;
                $childNodesLength = $childNodes->length;
                $firstValidNodePos = 0;
                do {
                } while (!(($childNodes->item($firstValidNodePos) instanceof \DOMNode) && $childNodes->item($firstValidNodePos)->nodeType === XML_ELEMENT_NODE) && $firstValidNodePos++ < $childNodesLength);
                if ($childNodes->item($firstValidNodePos)) {
                    $this->structs->addVirtualStruct($parentNode->getAttribute('name'));
                    for ($i = 0; $i < $childNodesLength; $i++) {
                        $childNode = $childNodes->item($i);
                        /**
                         * Not an enumeration restriction :
                         * <code>
                         * <xs:simpleType name="duration">
                         * -<xs:restriction base="xs:duration">
                         * --<xs:pattern value="\-?P(\d*D)?(T(\d*H)?(\d*M)?(\d*(\.\d*)?S)?)?"/>
                         * --<xs:minInclusive value="-P10675199DT2H48M5.4775808S"/>
                         * --<xs:maxInclusive value="P10675199DT2H48M5.4775807S"/>
                         * -</xs:restriction>
                         * </xs:simpleType>
                         * </code>
                         */
                        if ($childNode && stripos($childNode->nodeName, 'enumeration') === false && $childNode->hasAttributes()) {
                            $childNodeName = explode(':', $childNode->nodeName);
                            $childNodeName = $childNodeName[count($childNodeName) - 1];
                            $childNodeValue = $childNode->getAttribute('value');
                            $this->addStructMeta($parentNode->getAttribute('name'), $childNodeName, $childNodeValue);
                        }
                    }
                }
            }
        }
        self::audit('managewsdlnode_restriction', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
    }
    /**
     * Manages an enumeratio tag
     * @uses Generator::findSuitableParent()
     * @uses Generator::addRestrictionValue()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses Generator::getStruct()
     * @uses Generator::addStructMeta()
     * @uses AbstractModel::getMetaValue()
     * @uses \DOMElement::getAttribute()
     * @uses \DOMElement::hasAttribute()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @return void
     */
    protected function manageWsdlNodeEnumeration($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '')
    {
        self::auditInit('managewsdlnode_enumeration', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        $parentNode = self::findSuitableParent($domNode);
        if ($parentNode && $domNode->hasAttribute('value')) {
            if ($this->getStruct($parentNode->getAttribute('name')) && !$this->getStruct($parentNode->getAttribute('name'))->getFromSchema()) {
                $this->getStruct($parentNode->getAttribute('name'))->setFromSchema(!empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
            }
            $this->addRestrictionValue($parentNode->getAttribute('name'), $domNode->getAttribute('value'));
        }
        self::audit('managewsdlnode_enumeration', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
    }
    /**
     * Manages element method
     * @uses Generator::findSuitableParent()
     * @uses Generator::addStructAttributeMeta()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses Generator::addStructMeta()
     * @uses \DOMElement::getAttribute()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @return void
     */
    protected function manageWsdlNodeElement($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '')
    {
        self::auditInit('managewsdlnode_element', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        if ($this->getStruct($domNode->getAttribute('name'))) {
            $this->getStruct($domNode->getAttribute('name'))->setFromSchema(!empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        }
        self::audit('managewsdlnode_element', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
    }
    /**
     * Manages element method
     * @uses Generator::findSuitableParent()
     * @uses Generator::setStructAttributeDocumentation()
     * @uses Generator::setStructValueDocumentation()
     * @uses Generator::setStructDocumentation()
     * @uses Generator::setServiceFunctionDocumentation()
     * @uses Generator::addWsdlMeta()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMElement::getAttribute()
     * @uses \DOMElement::hasAttribute()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @return void
     */
    protected function manageWsdlNodeDocumentation($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '')
    {
        self::auditInit('managewsdlnode_documentation', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        $documentation = trim($domNode->nodeValue);
        $documentation = str_replace(array("\r", "\n", "\t"), array('', '', ' '), $documentation);
        $documentation = preg_replace('[\s+]', ' ', $documentation);
        /**
         * Finds parent node of this documentation node without taking care of the name attribute for enumeration and definitions
         * This case is managed first because enumerations are contained by elements and the method could climb to its parent without stopping on the enumeration tag
         * Go from the deepest possible node to the highest possible node
         * Each case must be treated on the same level, this is why we test the suitableParentNode for each case
         */
        $enumerationNode = self::findSuitableParent($domNode, false, array('enumeration'));
        $definitionsNode = self::findSuitableParent($domNode, false, array('definitions'));
        $attributeGroupNode = self::findSuitableParent($domNode, false, array('attributeGroup'));
        $anyNode = self::findSuitableParent($domNode, true, array('operation'));
        /**
         * is it an enumeration' value
         */
        if ($enumerationNode && stripos($enumerationNode->nodeName, 'enumeration') !== false) {
            /**
             * Finds parent node of this enumeration node
             */
            $upParentNode = self::findSuitableParent($enumerationNode);
            if ($upParentNode) {
                $this->setStructValueDocumentation($upParentNode->getAttribute('name'), $enumerationNode->getAttribute('value'), $documentation);
            }
        } elseif ($attributeGroupNode && stripos($attributeGroupNode->nodeName, 'attributeGroup') !== false) {
        } elseif ($anyNode && (stripos($anyNode->nodeName, 'element') !== false || stripos($anyNode->nodeName, 'attribute') !== false) && $anyNode->hasAttribute('type')) {
            /**
             * is it an element ? part of a struct
             * Finds parent node of this documentation node
             */
            $upParentNode = self::findSuitableParent($anyNode);
            if ($upParentNode) {
                $this->setStructAttributeDocumentation($upParentNode->getAttribute('name'), $anyNode->getAttribute('name'), $documentation);
            } elseif (stripos($anyNode->nodeName, 'element') !== false) {
                $this->setStructDocumentation($anyNode->getAttribute('name'), $documentation);
            }
        } elseif ($anyNode && (stripos($anyNode->nodeName, 'element') !== false || stripos($anyNode->nodeName, 'complextype') !== false || stripos($anyNode->nodeName, 'simpletype') !== false || stripos($anyNode->nodeName, 'attribute') !== false)) {
            /**
             * is it a struct ?
             */
            $this->setStructDocumentation($anyNode->getAttribute('name'), $documentation);
        } elseif ($anyNode && stripos($anyNode->nodeName, 'operation') !== false) {
            /**
             * is it an operation ?
             */
            $this->setServiceFunctionDocumentation($anyNode->getAttribute('name'), $documentation);
        } elseif ($definitionsNode && stripos($definitionsNode->nodeName, 'definitions') !== false) {
            /**
             * is it the definitions node of the WSDL
             */
            $this->addWsdlMeta($wsdlLocation, 'documentation', $documentation);
        }
        self::audit('managewsdlnode_documentation', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
    }
    /**
     * Manages extension method
     * @uses Generator::findSuitableParent()
     * @uses Generator::setStructInheritance()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMElement::hasAttribute()
     * @uses \DOMElement::getAttribute()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @return void
     */
    protected function manageWsdlNodeExtension($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '')
    {
        self::auditInit('managewsdlnode_extension', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        if ($domNode->hasAttribute('base')) {
            $base = explode(':', $domNode->getAttribute('base'));
            $inheritsName = $base[count($base) - 1];
            if (!empty($inheritsName)) {
                /**
                 * Finds parent node of this extension node
                 */
                $parentNode = self::findSuitableParent($domNode);
                if ($parentNode) {
                    /**
                     * Avoid infinite loop on case like this when looping/managing inheritance :
                     * <code>
                     * <xs:complexType name="duration">
                     * -<xs:simpleContent>
                     * --<xs:extension base="xs:duration">
                     * ---<xs:attributeGroup ref="tns:commonAttributes"/>
                     * --</xs:extension>
                     * -</xs:simpleContent>
                     * </xs:complexType>
                     * </code>
                     */
                    if ($inheritsName !== $parentNode->getAttribute('name')) {
                        $this->setStructInheritance($parentNode->getAttribute('name'), $inheritsName);
                    }
                }
            }
        }
        self::audit('managewsdlnode_extension', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
    }
    /**
     * Manages header node to extract informations about header types
     * @uses Generator::findSuitableParent()
     * @uses Generator::addServiceFunctionMeta()
     * @uses Generator::wsdlLocationToDomDocument()
     * @uses Generator::getStruct()
     * @uses Generator::getGlobal()
     * @uses Generator::setGlobal()
     * @uses Generator::getServiceMethod()
     * @uses Generator::executeDomXPathQuery()
     * @uses AbstractModel::getPackagedName()
     * @uses AbstractModel::getMetaValue()
     * @uses \DOMElement::getAttribute()
     * @uses \DOMElement::hasAttribute()
     * @uses \DOMNodeList::item()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNodeHeader($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        /**
         * Ensure current node is defined as the operation input
         */
        $parentNode = self::findSuitableParent($domNode, false, array('input'));
        if ($parentNode && stripos($parentNode->nodeName, 'input') !== false) {
            /**
             * Finds operation node
             */
            $parentNode = self::findSuitableParent($parentNode, true, array('operation'));
            if ($parentNode) {
                /**
                 * Header types and names
                 */
                $headerType = '';
                $headerName = $domNode->hasAttribute('part') ? $domNode->getAttribute('part') : '';
                $headerMessage = explode(':', $domNode->hasAttribute('message') ? $domNode->getAttribute('message') : '');
                $headerMessage = count($headerMessage) ? $headerMessage[count($headerMessage) - 1] : '';
                /**
                 * Finds it in the wsdls and avoid mutliple searches for the same message part
                 */
                if (!empty($headerName) && !empty($headerMessage) && $this->getServiceMethod($parentNode->getAttribute('name')) && !in_array($headerName, $this->getServiceMethod($parentNode->getAttribute('name'))->getMetaValue('SOAPHeaderNames', array()))) {
                    $notRequired = false;
                    $attributes = $domNode->attributes;
                    $attributesCount = $attributes->length;
                    for ($i = 0; $i < $attributesCount; $i++) {
                        if ($attributes->item($i) && stripos($attributes->item($i)->nodeName, 'required') !== false) {
                            $notRequired |= ($attributes->item($i)->nodeValue === 0 || $attributes->item($i)->nodeValue === 'false' || $attributes->item($i)->nodeValue === false || $attributes->item($i)->nodeValue === 'non' || $attributes->item($i)->nodeValue === 'no');
                        }
                    }
                    /**
                     * Header Namespace ?
                     */
                    $namespace = '';
                    if ($domNode->hasAttribute('namespace') && $domNode->getAttribute('namespace') != '') {
                        $namespace = $domNode->getAttribute('namespace');
                    }
                    $globalHeaderTypeKey = __METHOD__ . '_' . $headerMessage . '_' . $headerName . '_type';
                    $globalHeaderNameKey = __METHOD__ . '_' . $headerMessage . '_' . $headerName . '_name';
                    $globalHeaderNamespaceKey = __METHOD__ . '_' . $headerMessage . '_' . $headerName . '_namespace';
                    /**
                     * header name for the current message already known ?
                     */
                    $headerType = self::getGlobal($globalHeaderTypeKey, '');
                    $namespace = self::getGlobal($globalHeaderNamespaceKey, $namespace);
                    $headerName = self::getGlobal($globalHeaderNameKey, $headerName);
                    if (empty($headerType)) {
                        foreach ($this->getWsdls() as $wsdlLocation => $meta) {
                            $domDocument = self::wsdlLocationToDomDocument($wsdlLocation);
                            if ($domDocument instanceof \DOMDocument) {
                                /**
                                 * Gets part element
                                 */
                                $nodes = self::executeDomXPathQuery($domDocument, "//*[@name='$headerMessage']/*[@name='$headerName']");
                                $nodesLength = $nodes->length;
                                if ($nodesLength == 1 && ($nodes->item(0) instanceof \DOMNode) && stripos($nodes->item(0)->nodeName, 'part') !== false) {
                                    $part = $nodes->item(0);
                                    $partElement = '';
                                    $partNamespace = '';
                                    $partAttributes = array('element', 'type');
                                    foreach ($partAttributes as $partAttributeName) {
                                        if ($part->hasAttribute($partAttributeName)) {
                                            $partElements = explode(':', $part->getAttribute($partAttributeName));
                                            $partElement = count($partElements) ? $partElements[count($partElements) - 1] : '';
                                            $partNamespace = count($partElements) ? $partElements[0] : '';
                                            if (!empty($partElement)) {
                                                $headerName = $partElement;
                                                break;
                                            }
                                        }
                                    }
                                    if (!empty($partElement)) {
                                        /**
                                         * Finds element part in the WSDLs
                                         */
                                        foreach ($this->getWsdls() as $wsdlLocation => $meta) {
                                            $domDocument = self::wsdlLocationToDomDocument($wsdlLocation);
                                            if ($domDocument instanceof \DOMDocument) {
                                                /**
                                                 * Namespace value
                                                 */
                                                $definitions = self::findSuitableParent($part, false, array('definitions'));
                                                if ($definitions && $definitions->hasAttribute('xmlns:' . $partNamespace) && $definitions->getAttribute('xmlns:' . $partNamespace) != '') {
                                                    $namespace = $definitions->getAttribute('xmlns:' . $partNamespace);
                                                }
                                                /**
                                                 * Header type value
                                                 */
                                                $nodes = self::executeDomXPathQuery($domDocument, "//*[@name='$partElement']");
                                                $nodesLength = $nodes->length;
                                                $nodeIndex = 0;
                                                do {
                                                } while ($nodeIndex < $nodesLength && (!($nodes->item($nodeIndex) instanceof \DOMElement) || (($nodes->item($nodeIndex) instanceof \DOMElement) && (!$nodes->item($nodeIndex)->hasAttribute('type') || ($nodes->item($nodeIndex)->hasAttribute('type') && $nodes->item($nodeIndex)->getAttribute('type') === '')))) && $nodeIndex++);
                                                if ($nodeIndex <= $nodesLength && ($nodes->item($nodeIndex) instanceof \DOMElement) && $nodes->item($nodeIndex)->hasAttribute('type') && $nodes->item($nodeIndex)->getAttribute('type') != '') {
                                                    $headerType = explode(':', $nodes->item($nodeIndex)->getAttribute('type'));
                                                    $headerType = $headerType[count($headerType) - 1];
                                                    if ($this->getStruct($headerType) && $this->getStruct($headerType)->getIsStruct()) {
                                                        $headerType = '{@link ' . $this->getStruct($headerType)->getPackagedName() . '}';
                                                    }
                                                    break;
                                                }
                                            }
                                        }
                                        /**
                                         * Element type not found, then it's maybe an already known struct ?
                                         */
                                        if (empty($headerType) && $this->getStruct($partElement) && $this->getStruct($partElement)->getIsStruct()) {
                                            $headerType = '{@link ' . $this->getStruct($partElement)->getPackagedName() . '}';
                                        }
                                    }
                                }
                            }
                            self::setGlobal($globalHeaderNameKey, $headerName);
                            if (!empty($namespace)) {
                                self::setGlobal($globalHeaderNamespaceKey, $namespace);
                            }
                            if (!empty($headerType)) {
                                self::setGlobal($globalHeaderTypeKey, $headerType);
                                break;
                            }
                        }
                    }
                    /**
                     * Indicate that header is required for this operation
                     */
                    $this->addServiceFunctionMeta($parentNode->getAttribute('name'), 'SOAPHeaders', array($notRequired ? 'optional' : 'required'));
                    /**
                     * Indicate the required header name
                     */
                    $this->addServiceFunctionMeta($parentNode->getAttribute('name'), 'SOAPHeaderNames', array($headerName));
                    /**
                     * Indicate the required header type
                     */
                    $this->addServiceFunctionMeta($parentNode->getAttribute('name'), 'SOAPHeaderTypes', array($headerType));
                    /**
                     * Indicate the required header namespace
                     */
                    $this->addServiceFunctionMeta($parentNode->getAttribute('name'), 'SOAPHeaderNamespaces', array($namespace));
                }
            }
        }
    }
    /**
     * Manages attribute node to extract informations about its type if \SoapClient didn't succeed to determine it
     * @uses \DOMElement::hasAttribute()
     * @uses \DOMElement::getAttribute()
     * @uses Generator::findSuitableParent()
     * @uses Generator::getStructAttribute()
     * @uses Generator::getStruct()
     * @uses Generator::addStructMeta()
     * @uses AbstractModel::getMetaValue()
     * @uses AbstractModel::getModelByName()
     * @uses AbstractModel::getInheritance()
     * @uses Struct::getIsStruct()
     * @uses StructAttribute::getType()
     * @uses StructAttribute::setType()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNodeAttribute($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        if ($nodeNameMatch === 'attribute') {
            if (($domNode instanceof \DOMElement) && $domNode->hasAttribute('name') && $domNode->getAttribute('name') && $domNode->hasAttribute('type') && $domNode->getAttribute('type')) {
                $parentNode = self::findSuitableParent($domNode);
                if ($parentNode) {
                    $attributeModel = $this->getStructAttribute($parentNode->getAttribute('name'), $domNode->getAttribute('name'));
                    $type = explode(':', $domNode->getAttribute('type'));
                    $typeModel = AbstractModel::getModelByName($type[count($type) - 1]);
                    if ($attributeModel && (!$attributeModel->getType() || strtolower($attributeModel->getType()) == 'unknown') && $typeModel) {
                        if ($typeModel->getIsRestriction()) {
                            $attributeModel->setType($typeModel->getName());
                        } elseif (!$typeModel->getIsStruct() && $typeModel->getInheritance()) {
                            $attributeModel->setType($typeModel->getInheritance());
                        }
                    }
                }
            }
        } else {
            self::auditInit('managewsdlnode_attribute', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
            /**
             * Finds parent node of this element node
             */
            $parentNode = self::findSuitableParent($domNode);
            if ($parentNode) {
                if ($this->getStruct($parentNode->getAttribute('name')) && !$this->getStruct($parentNode->getAttribute('name'))->getFromSchema()) {
                    $this->getStruct($parentNode->getAttribute('name'))->setFromSchema(!empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
                }
                $attributes = $domNode->attributes;
                $attributesLength = $attributes->length;
                for ($i = 0; $i < $attributesLength; $i++) {
                    $attribute = $attributes->item($i);
                    if ($attribute && $attribute->nodeName != 'name' && $attribute->nodeName != 'type') {
                        $this->addStructAttributeMeta($parentNode->getAttribute('name'), $domNode->getAttribute('name'), $attribute->nodeName, $attribute->nodeValue);
                    }
                }
            }
            self::audit('managewsdlnode_attribute', !empty($wsdlLocation) ? $wsdlLocation : $fromWsdlLocation);
        }
    }
    /**
     * Manages union node
     * @uses Generator::findSuitableParent()
     * @uses Generator::setStructInheritance()
     * @uses \DOMNode::hasAttributes()
     * @uses \DOMNodeList::item()
     * @uses \DOMElement::getAttribute()
     * @uses AbstractModel::getModelByName()
     * @uses AbstractModel::getInheritance()
     * @uses AbstractModel::getName()
     * @uses Struct::getIsStruct()
     * @uses Struct::getIsRestriction()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNodeUnion($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        if ($domNode->hasAttributes()) {
            $parentNode = self::findSuitableParent($domNode);
            if ($parentNode) {
                $parentNodeStruct = $this->getStruct($parentNode->getAttribute('name'));
                $attributes = $domNode->attributes;
                $attributesCount = $attributes->length;
                for ($i = 0; $i < $attributesCount; $i++) {
                    $attribute = $attributes->item($i);
                    if ($attribute && stripos($attribute->nodeName, 'membertypes') !== false) {
                        $nodeValue = $attribute->nodeValue;
                        $nodeValues = explode(' ', $nodeValue);
                        if (count($nodeValues)) {
                            $nodeValueTypes = array();
                            foreach ($nodeValues as $nodeValueType) {
                                $nodeValueType = explode(':', $nodeValueType);
                                $nodeValueType = trim($nodeValueType[count($nodeValueType) - 1]);
                                if (!empty($nodeValueType)) {
                                    $this->addStructMeta($parentNode->getAttribute('name'), 'union', array($nodeValueType));
                                    $nodeValueTypeModel = AbstractModel::getModelByName($nodeValueType);
                                    while ($nodeValueTypeModel) {
                                        if ($nodeValueTypeModel->getIsRestriction()) {
                                            $nodeValueType = $nodeValueTypeModel->getName();
                                            $nodeValueTypeModel = null;
                                        } elseif ($nodeValueTypeModel->getInheritance()) {
                                            $newNodeValueTypeModel = AbstractModel::getModelByName($nodeValueTypeModel->getInheritance());
                                            if (!$newNodeValueTypeModel)
                                                $nodeValueType = $nodeValueTypeModel->getInheritance();
                                            $nodeValueTypeModel = $newNodeValueTypeModel;
                                        } else {
                                            $nodeValueTypeModel = null;
                                        }
                                    }
                                    array_push($nodeValueTypes, $nodeValueType);
                                }
                            }
                            $nodeValueTypes = array_unique($nodeValueTypes);
                            if (count($nodeValueTypes) && $parentNodeStruct && !$parentNodeStruct->getInheritance()) {
                                $this->setStructInheritance($parentNodeStruct->getName(), implode(', ', $nodeValueTypes));
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * Manages list node
     * @uses Generator::findSuitableParent()
     * @uses Generator::setStructInheritance()
     * @uses \DOMNode::hasAttributes()
     * @uses \DOMNodeList::item()
     * @uses \DOMElement::getAttribute()
     * @uses AbstractModel::getName()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNodeList($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        if ($domNode->hasAttributes()) {
            $parentNode = self::findSuitableParent($domNode);
            if ($parentNode) {
                $parentNodeStruct = $this->getStruct($parentNode->getAttribute('name'));
                $attributes = $domNode->attributes;
                $attributesCount = $attributes->length;
                for ($i = 0; $i < $attributesCount; $i++) {
                    $attribute = $attributes->item($i);
                    if ($attribute && stripos($attribute->nodeName, 'itemType') !== false) {
                        $nodeValue = trim($attribute->nodeValue);
                        if ($this->getStruct($nodeValue)) {
                            $this->setStructInheritance($parentNode->getAttribute('name'), 'array of ' . $this->getStruct($nodeValue)->getName());
                        }
                    }
                }
            }
        }
    }
    /**
     * Manages input node
     * @uses Generator::manageWsdlNodeInputOutput()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNodeInput($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        $this->manageWsdlNodeInputOutput($wsdlLocation, $domNode, $fromWsdlLocation, $nodeNameMatch);
    }
    /**
     * Manages output node
     * @uses Generator::manageWsdlNodeInputOutput()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNodeOutput($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        $this->manageWsdlNodeInputOutput($wsdlLocation, $domNode, $fromWsdlLocation, $nodeNameMatch);
    }
    /**
     * Manages input/output node
     * @uses Generator::findSuitableParent()
     * @uses Generator::getServiceMethod()
     * @uses Generator::executeDomXPathQuery()
     * @uses \DOMNode::hasAttributes()
     * @uses \DOMNodeList::item()
     * @uses \DOMElement::getAttribute()
     * @uses \DOMElement::hasAttribute()
     * @uses Method::getParameterType()
     * @uses Method::setParameterType()
     * @uses Method::getReturnType()
     * @uses Method::setReturnType()
     * @param string $wsdlLocation the wsdl location
     * @param \DOMNode $domNode the node
     * @param string $fromWsdlLocation the wsdl location imported
     * @param string $nodeNameMatch the name the node name must match, only when it's necessary to match a certain type of nodes
     * @return void
     */
    protected function manageWsdlNodeInputOutput($wsdlLocation = '', \DOMNode $domNode, $fromWsdlLocation = '', $nodeNameMatch = null)
    {
        if ($domNode->hasAttribute('message') && $domNode->getAttribute('message') != '' && ($nodeNameMatch === 'input' || $nodeNameMatch === 'output')) {
            $messageName = explode(':', $domNode->getAttribute('message'));
            $messageName = $messageName[count($messageName) - 1];
            $parentNode = self::findSuitableParent($domNode, true, array('operation'));
            if (!empty($messageName) && $parentNode) {
                $operationName = $parentNode->getAttribute('name');
                if ($this->getServiceMethod($operationName)) {
                    if ($nodeNameMatch == 'input') {
                        $operationParameterReturnType = $this->getServiceMethod($operationName)->getParameterType();
                    } else {
                        $operationParameterReturnType = $this->getServiceMethod($operationName)->getReturnType();
                    }
                    $operationParameterReturnTypeKnown = true;
                    if (is_string($operationParameterReturnType) && (empty($operationParameterReturnType) || strtolower($operationParameterReturnType) === 'unknown')) {
                        $operationParameterReturnTypeKnown = false;
                        $operationParameterReturnTypeFound = '';
                    } elseif (is_array($operationParameterReturnType)) {
                        foreach ($operationParameterReturnType as $parameterType) {
                            $operationParameterReturnTypeKnown &= (!empty($parameterType) && !(strtolower($parameterType) === 'unknown'));
                        }
                        $operationParameterReturnTypeFound = array();
                    }
                    /**
                     * Parameter type is unknown, then find message among the WSDLs
                     */
                    if (!$operationParameterReturnTypeKnown) {
                        $operationParameterReturnTypeDefined = false;
                        foreach ($this->getWsdls() as $wsdlLocation => $meta) {
                            $domDocument = self::wsdlLocationToDomDocument($wsdlLocation);
                            if ($domDocument instanceof \DOMDocument) {
                                $nodes = self::executeDomXPathQuery($domDocument, "//*[@name='$messageName']");
                                $nodesLength = $nodes->length;
                                $nodeIndex = 0;
                                do {
                                } while ($nodeIndex < $nodesLength && (!($nodes->item($nodeIndex) instanceof \DOMElement) || (($nodes->item($nodeIndex) instanceof \DOMElement) && stripos($nodes->item($nodeIndex)->nodeName, 'message') === false)) && $nodeIndex++);
                                /**
                                 * Message definition found, then find its corresponding element
                                 */
                                if ($nodeIndex <= $nodesLength && ($nodes->item($nodeIndex) instanceof \DOMElement) && stripos($nodes->item($nodeIndex)->nodeName, 'message') !== false && $nodes->item($nodeIndex)->hasChildNodes()) {
                                    $childNodes = $nodes->item($nodeIndex)->childNodes;
                                    $childNodesCount = $childNodes->length;
                                    for ($i = 0; $i < $childNodesCount; $i++) {
                                        $child = $childNodes->item($i);
                                        if ($child && stripos($child->nodeName, 'part') !== false && $child->hasAttribute('element') && $child->getAttribute('element') !== '') {
                                            $partElement = '';
                                            $partAttributes = array('element', 'type');
                                            foreach ($partAttributes as $partAttributeName) {
                                                if ($child->hasAttribute($partAttributeName)) {
                                                    $partElements = explode(':', $child->getAttribute($partAttributeName));
                                                    $partElement = count($partElements) ? $partElements[count($partElements) - 1] : '';
                                                    if (!empty($partElement)) {
                                                        break;
                                                    }
                                                }
                                            }
                                            if (!empty($partElement)) {
                                                /**
                                                 * Finds element part in the WSDLs
                                                 */
                                                foreach ($this->getWsdls() as $wsdlLocation => $meta) {
                                                    $domDocument = self::wsdlLocationToDomDocument($wsdlLocation);
                                                    if ($domDocument instanceof \DOMDocument) {
                                                        $nodes = self::executeDomXPathQuery($domDocument, "//*[@name='$partElement']");
                                                        $nodesLength = $nodes->length;
                                                        $nodeIndex = 0;
                                                        do {
                                                        } while ($nodeIndex < $nodesLength && (!($nodes->item($nodeIndex) instanceof \DOMElement) || (($nodes->item($nodeIndex) instanceof \DOMElement) && (!$nodes->item($nodeIndex)->hasAttribute('type') || ($nodes->item($nodeIndex)->hasAttribute('type') && $nodes->item($nodeIndex)->getAttribute('type') === '')))) && $nodeIndex++);
                                                        if ($nodeIndex <= $nodesLength && ($nodes->item($nodeIndex) instanceof \DOMElement) && $nodes->item($nodeIndex)->hasAttribute('type') && $nodes->item($nodeIndex)->getAttribute('type') != '') {
                                                            $parameterType = explode(':', $nodes->item($nodeIndex)->getAttribute('type'));
                                                            $parameterType = $parameterType[count($parameterType) - 1];
                                                            if (!empty($parameterType)) {
                                                                if (is_string($operationParameterReturnType)) {
                                                                    $operationParameterReturnTypeFound = $parameterType;
                                                                    $operationParameterReturnTypeDefined = true;
                                                                } else {
                                                                    array_push($operationParameterReturnTypeFound, $parameterType);
                                                                    if (count($operationParameterReturnTypeFound) == count($operationParameterReturnType))
                                                                        $operationParameterReturnTypeDefined = true;
                                                                }
                                                            }
                                                        } else {
                                                            $nodeIndex = 0;
                                                            while (!$operationParameterReturnTypeDefined && $nodeIndex < $nodesLength) {
                                                                $node = $nodes->item($nodeIndex);
                                                                if ($node && !empty($node->nodeName) && $node->getAttribute('name') == $partElement && (stripos($node->nodeName, 'element') !== false || stripos($node->nodeName, 'complexType') !== false || stripos($node->nodeName, 'simpleType') !== false)) {
                                                                    $operationParameterReturnTypeFound = $node->getAttribute('name');
                                                                    $operationParameterReturnTypeDefined = true;
                                                                }
                                                                $nodeIndex++;
                                                            }
                                                        }
                                                    }
                                                    if ($operationParameterReturnTypeDefined) {
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($operationParameterReturnTypeDefined) {
                                            break;
                                        }
                                    }
                                }
                            }
                            if ($operationParameterReturnTypeDefined) {
                                break;
                            }
                        }
                        /**
                         * Operation parameter type found, then define it
                         */
                        if ($operationParameterReturnTypeDefined) {
                            if ($nodeNameMatch == 'input') {
                                $this->getServiceMethod($operationName)->setParameterType($operationParameterReturnTypeFound);
                            } else {
                                $this->getServiceMethod($operationName)->setReturnType($operationParameterReturnTypeFound);
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * Finds the suitable parent node of the current node in maximum 5 parents
     * Centralize method to find a valid parent
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMElement::getAttribute()
     * @uses \DOMElement::hasAttribute()
     * @param \DOMNode $domNode
     * @param bool $checkName whether to validate the attribute named "name" or not
     * @param array $parentTags parent tags name to fit a parent tag
     * @param int $maxDeep max deep of this current node
     * @return \DOMElement|null
     */
    final private static function findSuitableParent(\DOMNode $domNode, $checkName = true, array $parentTags = array(), $maxDeep = 5)
    {
        self::auditInit(__METHOD__, $domNode->nodeName);
        $parentTags = array_merge(array('element', 'complexType', 'simpleType', 'attribute'), $parentTags);
        $parentNode = $domNode->parentNode;
        while ($maxDeep-- > 0 && ($parentNode instanceof \DOMElement) && $parentNode->nodeName && (!preg_match('/' . implode('|', $parentTags) . '/i', $parentNode->nodeName) || ($checkName && preg_match('/' . implode('|', $parentTags) . '/i', $parentNode->nodeName) && (!$parentNode->hasAttribute('name') || $parentNode->getAttribute('name') == '')))) {
            $parentNode = $parentNode->parentNode;
        }
        self::audit(__METHOD__, $domNode->nodeName);
        return ($parentNode instanceof \DOMElement) ? $parentNode : null;
    }
    /**
     * Execute query on \DOMDocument using \DOMXPath
     * @uses \DOMXPath::query()
     * @param \DOMDocument $domDocument the \DOMDocument to execute the query on
     * @param string $query the query to execute
     * @return \DOMNodeList the results
     */
    public static function executeDomXPathQuery(\DOMDocument $domDocument, $query)
    {
        $domXPath = new \DOMXPath($domDocument);
        return $domXPath->query($query);
    }
    /**
     * Returns the \DOMDocument object for a wsdl location
     * @uses Generator::getGlobal()
     * @uses Generator::setGlobal()
     * @uses Generator::auditInit()
     * @uses Generator::audit()
     * @uses \DOMDocument::saveXML()
     * @uses \DOMDocument::loadXML()
     * @param string $wsdlLocation the wsdl location
     * @return \DOMDocument|null
     */
    final private static function wsdlLocationToDomDocument($wsdlLocation)
    {
        self::auditInit(__METHOD__, $wsdlLocation);
        $globalKey = __METHOD__ . '_' . $wsdlLocation;
        $dom = self::getGlobal($globalKey);
        if (!($dom instanceof \DOMDocument)) {
            $wsdlLocationContent = self::instance()->getUrlContent($wsdlLocation);
            $dom = new \DOMDocument('1.0', 'UTF-8');
            /**
             * Comments tag on the beginning block parsing the \DOMDocument
             */
            if (empty($wsdlLocationContent) || trim($wsdlLocationContent) == '<?xml version="1.0" encoding="UTF-8"?>') {
                $wsdlLocationContent = preg_replace('(<!--.*-->)', '', $wsdlLocationContent);
            }
            if (!empty($wsdlLocationContent)) {
                $dom->loadXML($wsdlLocationContent);
            } else {
                $dom = null;
            }
            self::setGlobal($globalKey, $dom);
        }
        self::audit(__METHOD__, $wsdlLocation);
        return $dom;
    }
    /**
     * Returns directory where to store class and create it if needed
     * @uses Generator::getCategory()
     * @uses Generator::getSubCategory()
     * @param string $rootDirectory the directory
     * @param int $rootDirectoryRights the permissions to apply
     * @param AbstractModel $model the model for which we generate the folder
     * @return string
     */
    private function getDirectory($rootDirectory, $rootDirectoryRights, AbstractModel $model)
    {
        $directory = $rootDirectory;
        $mainCat = $this->getCategory($model);
        $subCat = $this->getSubCategory($model);
        if (!empty($mainCat)) {
            $directory .= ucfirst($mainCat) . '/';
            if (!is_dir($directory)) {
                mkdir($directory, $rootDirectoryRights);
            }
        }
        if (!empty($subCat)) {
            $directory .= ucfirst($subCat) . '/';
            if (!is_dir($directory)) {
                mkdir($directory, $rootDirectoryRights);
            }
        }
        return $directory;
    }
    /**
     * Gets main category part
     * @param AbstractModel $model the model for which we generate the folder
     * @return string
     */
    private function getCategory(AbstractModel $model)
    {
        return Utils::getPart($this->options, $model, GeneratorOptions::CATEGORY);
    }
    /**
     * Gets sub category part
     * @param AbstractModel $model the model for which we generate the folder
     * @return string
     */
    private function getSubCategory(AbstractModel $model)
    {
        return Utils::getPart($this->options, $model, GeneratorOptions::SUB_CATEGORY);
    }
    /**
     * Gets gather name class
     * @param AbstractModel $model the model for which we generate the folder
     * @return string
     */
    private function getGather(AbstractModel $model)
    {
        return Utils::getPart($this->options, $model, GeneratorOptions::GATHER_METHODS);
    }
    /**
     * Returns the service name associated to the method/operation name in order to gather them in one service class
     * @uses Generator::getGather()
     * @param string $methodName original operation/method name
     * @return string
     */
    public function getServiceName($methodName)
    {
        return ucfirst($this->getGather(new EmptyModel($methodName)));
    }
    /**
     * Inits global array dedicated to the class
     * @uses Generator::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY
     * @return bool true
     */
    private static function initGlobals()
    {
        self::$globals[self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY] = array();
        return true;
    }
    /**
     * Clears the global array dedicated the the class
     * @uses Generator::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY
     * @return bool true
     */
    public static function unsetGlobals()
    {
        if (array_key_exists(self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY, self::$globals))
            unset(self::$globals[self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY]);
        return true;
    }
    /**
     * Sets a global value
     * @uses Generator::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY
     * @param scalar $globalKey the index where to store the data in the global array dedicated the the class
     * @param mixed $globalValue the value to store
     * @return mixed
     */
    private static function setGlobal($globalKey, $globalValue)
    {
        if (!is_scalar($globalKey)) {
            return null;
        }
        if (array_key_exists(self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY, self::$globals)) {
            return (self::$globals[self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY][$globalKey] = $globalValue);
        } else {
            return null;
        }
    }
    /**
     * Gets a global value
     * @uses Generator::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY
     * @param scalar $globalKey the index where to store the data in the global array dedicated the the class
     * @param mixed $globalFallback the fallback value
     * @return mixed
     */
    private static function getGlobal($globalKey, $globalFallback = null)
    {
        if (!is_scalar($globalKey)) {
            return $globalFallback;
        }
        if (array_key_exists(self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY, self::$globals) && array_key_exists($globalKey, self::$globals[self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY])) {
            return self::$globals[self::WSDL_TO_PHP_GENERATOR_GLOBAL_KEY][$globalKey];
        } else {
            return $globalFallback;
        }
    }
    /**
     * Method to store audit timing during the process
     * @uses Generator::WSDL_TO_PHP_GENERATOR_AUDIT_KEY
     * @uses Generator::getGlobal()
     * @uses Generator::setGlobal()
     * @param string $auditName the type of audit (parsing, generating, etc..). If audit name is parsing_DOM, than parsing is created to cumulate time for all parsing processes
     * @param string $auditElement audit specific element
     * @param int $spentTime already spent time on the current audit category (and element)
     * @param bool $createOnly indicates if the element must be only created or not
     * @return bool true
     */
    private static function audit($auditName, $auditElement = '', $spentTime = 0, $createOnly = false)
    {
        if (!is_scalar($auditName) || empty($auditName)) {
            return false;
        }
        /**
         * Current time used
         */
        $time = time();
        /**
         * Variables contained by an audit entry
         */
        $variables = array('spent_time' => $spentTime, 'last_time' => $time, 'calls' => 0);
        /**
         * Audit content
         */
        $audit = self::getGlobal(self::WSDL_TO_PHP_GENERATOR_AUDIT_KEY, array());
        /**
         * Main audit category based on the current audit
         */
        if (strpos($auditName, '_')) {
            $mainAuditName = '';
            $mainAuditName = implode('', array_slice(explode('_', $auditName), 0, 1));
            if (!empty($mainAuditName)) {
                if (!array_key_exists($mainAuditName, $audit)) {
                    $audit[$mainAuditName] = $variables;
                } elseif (!$createOnly) {
                    $audit[$mainAuditName]['spent_time'] += $spentTime > 0 ? $spentTime : ($time - $audit[$mainAuditName]['last_time']);
                    $audit[$mainAuditName]['last_time'] = $time;
                    $audit[$mainAuditName]['calls']++;
                } else {
                    $audit[$mainAuditName]['last_time'] = $time;
                }
            }
        }
        /**
         * Current audit name
         */
        if (!array_key_exists($auditName, $audit)) {
            $audit[$auditName] = array('own' => $variables, 'elements' => array());
        }
        elseif (!$createOnly) {
            $audit[$auditName]['own']['spent_time'] += $spentTime > 0 ? $spentTime : ($time - $audit[$auditName]['own']['last_time']);
            $audit[$auditName]['own']['last_time'] = $time;
            $audit[$auditName]['own']['calls']++;
        } else {
            $audit[$auditName]['own']['last_time'] = $time;
        }
        /**
         * Current audit element
         */
        if (!empty($auditElement)) {
            if (!array_key_exists($auditElement, $audit[$auditName]['elements'])) {
                $audit[$auditName]['elements'][$auditElement] = $variables;
            }
            elseif (!$createOnly) {
                $audit[$auditName]['elements'][$auditElement]['spent_time'] += $spentTime > 0 ? $spentTime : ($time - $audit[$auditName]['elements'][$auditElement]['last_time']);
                $audit[$auditName]['elements'][$auditElement]['last_time'] = $time;
                $audit[$auditName]['elements'][$auditElement]['calls']++;
            } else {
                $audit[$auditName]['elements'][$auditElement]['last_time'] = $time;
            }
        }
        /**
         * Update global audit
         */
        self::setGlobal(self::WSDL_TO_PHP_GENERATOR_AUDIT_KEY, $audit);
        return true;
    }
    /**
     * Method to initialize audit for an element
     * @uses Generator::audit()
     * @param string $auditName the type of audit (parsing, generating, etc..). If audit name is parsing_DOM, than parsing is created to cumulate time for all parsing processes
     * @param string $auditElement audit specific element
     * @return bool true
     */
    private static function auditInit($auditName, $auditElement = '')
    {
        return self::audit($auditName, $auditElement, 0, true);
    }
    /**
     * Returns the audit informations
     * @uses Generator::getGlobal()
     * @uses Generator::WSDL_TO_PHP_GENERATOR_AUDIT_KEY
     * @return array
     */
    public static function getAudit()
    {
        return self::getGlobal(self::WSDL_TO_PHP_GENERATOR_AUDIT_KEY, array());
    }
    /**
     * @param GeneratorOptions $options
     * @return Generator
     */
    protected function setOptions(GeneratorOptions $options)
    {
        $this->options = $options;
        return $this;
    }
    /**
     * @return GeneratorOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
    /**
     * @param StructContainer $structContainer
     * @return Generator
     */
    protected function setStructs(StructContainer $structContainer)
    {
        $this->structs = $structContainer;
        return $this;
    }
    /**
     * @return StructContainer
     */
    public function getStructs()
    {
        return $this->structs;
    }
    /**
     * @return ServiceContainer
     */
    public function getServices()
    {
        return $this->services;
    }
    /**
     * @param ServiceContainer $serviceContainer
     * @return Generator
     */
    protected function setServices(ServiceContainer $serviceContainer)
    {
        $this->services = $serviceContainer;
        return $this;
    }
    /**
     * @param string $url
     * @return string
     */
    public function getUrlContent($url)
    {
        if (strpos($url, '://') !== false) {
            return Utils::getContentFromUrl($url, isset($this->_proxy_host) ? $this->_proxy_host : null, isset($this->_proxy_port) ? $this->_proxy_port : null, isset($this->_proxy_login) ? $this->_proxy_login : null, isset($this->_proxy_password) ? $this->_proxy_password : null);
        } elseif (is_file($url)) {
            return file_get_contents($url);
        }
        return null;
    }
    /**
     * Returns current class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
