clear;

rm -rf /var/www/workspace/FGS/Enum /var/www/workspace/FGS/Service /var/www/workspace/FGS/Struct  /var/www/workspace/FGS/*.php;
 
php console wsdltophp:generate:package \
    --wsdl-urlorpath="https://testservices.aviationcloud.aero/FlightGenerationService.svc?wsdl" \
    --wsdl-proxy-host="nrs-proxy02.ad-subs.w2k.francetelecom.fr" \
    --wsdl-proxy-port=3128 \
    --wsdl-proxy-login="fzcd1760" \
    --wsdl-proxy-password="losled@IT2015" \
    --wsdl-destination='/var/www/workspace/FGS/' \
    --wsdl-prefix="FGS" \
    --force