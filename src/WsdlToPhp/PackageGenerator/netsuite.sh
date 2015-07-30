rm -rf /var/www/workspace/Api/*.php \
        /var/www/workspace/Api/Enum* \
        /var/www/workspace/Api/Struct* \
        /var/www/workspace/Api/Service* \
        /var/www/workspace/Api/Array*;

php console wsdltophp:generate:package \
    --wsdl-urlorpath="https://webservices.netsuite.com/wsdl/v2015_1_0/netsuite.wsdl" \
    --wsdl-destination="/var/www/workspace/Api" \
    --wsdl-prefix="Api" \
    --force