clear;

rm -rf  /var/www/workspace/Api/Enum* \
        /var/www/workspace/Api/Array* \
        /var/www/workspace/Api/Service* \
        /var/www/workspace/Api/Struct* \
        /var/www/workspace/Api/composer.* \
        /var/www/workspace/Api/vendor \
        /var/www/workspace/Api/*.php;

# https://www.paypalobjects.com/wsdl/PayPalSvc.wsdl
# 
# http://demo.magentocommerce.com/api/v2_soap?wsdl=1
# http://www.maxmind.com/app/minfraud_soap_wsdl14

php console wsdltophp:generate:package \
    --wsdl-urlorpath="Tests/resources/ews.wsdl" \
    --wsdl-destination='/var/www/workspace/Api/' \
    --wsdl-prefix="Api" \
    --force
