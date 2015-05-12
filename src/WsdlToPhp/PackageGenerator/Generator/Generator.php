<?php

namespace WsdlToPhp\PackageGenerator\Generator;

use WsdlToPhp\PackageGenerator\Model\Schema;
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
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagAttribute as TagAttributeParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagDocumentation as TagDocumentationParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagElement as TagElementParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagEnumeration as TagEnumerationParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagExtension as TagExtensionParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagHeader as TagHeaderParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagImport as TagImportParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagInclude as TagIncludeParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagInput as TagInputParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagList as TagListParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagOutput as TagOutputParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagRestriction as TagRestrictionParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagUnion as TagUnionParser;
use WsdlToPhp\PackageGenerator\Container\Parser as ParserContainer;
use WsdlToPhp\PackageGenerator\Parser\AbstractParser;

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
     * Used parsers
     * @var ParserContainer
     */
    private $parsers;
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
        $this->setParser(new ParserContainer());
        /**
         * add parsers
         */
        $this->addParser(new FunctionsParser($this));
        $this->addParser(new StructsParser($this));
        $this->addParser(new TagIncludeParser($this));
        $this->addParser(new TagImportParser($this));
        $this->addParser(new TagAttributeParser($this));
        $this->addParser(new TagDocumentationParser($this));
        $this->addParser(new TagElementParser($this));
        $this->addParser(new TagEnumerationParser($this));
        $this->addParser(new TagExtensionParser($this));
        $this->addParser(new TagHeaderParser($this));
        $this->addParser(new TagInputParser($this));
        $this->addParser(new TagOutputParser($this));
        $this->addParser(new TagRestrictionParser($this));
        $this->addParser(new TagUnionParser($this));
        /**
         * add WSDL
         */
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
                foreach ($this->parsers as $parser) {
                    self::auditInit(get_class($parser), $wsdlLocation);
                    $parser->parse();
                    self::audit(get_class($parser), $wsdlLocation);
                }
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
     * Adds the service function a meta information
     * @uses Generator::getServiceMethod()
     * @uses AbstractModel::addMeta()
     * @param string $methodName the service name
     * @param string $methodInfoName the method name
     * @param string $methodInfoValue the method info value
     * @return void
     */
    public function addServiceFunctionMeta($methodName, $methodInfoName, $methodInfoValue)
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
     * Adds Wsdl location
     * @param Wsdl $wsdl
     * @param string $schemaLocation
     */
    public function addSchemaToWsdl(Wsdl $wsdl, $schemaLocation)
    {
        if (!empty($schemaLocation) && $wsdl->getContent() !== null && $wsdl->getContent()->getExternalSchema($schemaLocation) === null) {
            $wsdl->getContent()->addExternalSchema(new Schema($schemaLocation, $this->getUrlContent($schemaLocation)));
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
     * @param ParserContainer $container
     * @return \WsdlToPhp\PackageGenerator\Generator\Generator
     */
    protected function setParser(ParserContainer $container)
    {
        $this->parsers = $container;
        return $this;
    }
    /**
     * @param AbstractParser; $parser
     * @return \WsdlToPhp\PackageGenerator\Generator\Generator
     */
    protected function addParser(AbstractParser $parser)
    {
        $this->parsers->add($parser);
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
