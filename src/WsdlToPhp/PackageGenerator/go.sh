rm -rf /var/www/workspace/go/Api/*.php \
        /var/www/workspace/go/Api/Struct* \
        /var/www/workspace/go/Api/Array* \
        /var/www/workspace/go/Api/Enum* \
        /var/www/workspace/go/Api/Service*;

php console wsdltophp:generate:package \
    --wsdl-urlorpath="/var/www/workspace/go/AccWSDLPostOnly.wsdl" \
    --wsdl-destination="/var/www/workspace/go/Api" \
    --wsdl-prefix="Api" \
    --force