rm -rf /var/www/workspace/Api/*.php \
        /var/www/workspace/Api/Enum* \
        /var/www/workspace/Api/Struct* \
        /var/www/workspace/Api/Service* \
        /var/www/workspace/Api/Array*;

php console wsdltophp:generate:package \
    --wsdl-urlorpath="http://secapp.euroconsumers.org/partnerservice/PartnerService.svc?wsdl" \
    --wsdl-destination="/var/www/workspace/Api" \
    --wsdl-prefix="Api" \
    --force