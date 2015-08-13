clear;

rm -rf  /var/www/workspace/Api/Enum* \
        /var/www/workspace/Api/Array* \
        /var/www/workspace/Api/Service* \
        /var/www/workspace/Api/Struct* \
        /var/www/workspace/Api/composer.* \
        /var/www/workspace/Api/vendor \
        /var/www/workspace/Api/*.php;

# https://www.paypalobjects.com/wsdl/PayPalSvc.wsdl
# http://ar.mtech-ltd.co.uk/csp/TRT/Web.OrderService.CLS?WSDL=1
# http://demo.magentocommerce.com/api/v2_soap?wsdl=1
# http://www.maxmind.com/app/minfraud_soap_wsdl14

php console wsdltophp:generate:package \
    --wsdl-urlorpath="http://demo.magentocommerce.com/api/v2_soap?wsdl=1" \
    --wsdl-proxy-host="nrs-proxy02.ad-subs.w2k.francetelecom.fr" \
    --wsdl-proxy-port=3128 \
    --wsdl-proxy-login="fzcd1760" \
    --wsdl-proxy-password="losled@IT2015" \
    --wsdl-destination='/var/www/workspace/Api/' \
    --wsdl-prefix="Api" \
    --wsdl-struct='\Std\Opt\StructClass' \
    --wsdl-structarray='\Std\Opt\StructArrayClass' \
    --wsdl-soapclient='\Std\Opt\SoapClientClass' \
    --force