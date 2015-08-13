clear;

rm -rf /var/www/workspace/Fungku/Enum /var/www/workspace/Fungku/Service /var/www/workspace/Fungku/Struct  /var/www/workspace/Fungku/*.php;
 
php console wsdltophp:generate:package \
    --wsdl-urlorpath="https://webservices.netsuite.com/wsdl/v2015_1_0/netsuite.wsdl" \
    --wsdl-proxy-host="nrs-proxy02.ad-subs.w2k.francetelecom.fr" \
    --wsdl-proxy-port=3128 \
    --wsdl-proxy-login="fzcd1760" \
    --wsdl-proxy-password="losled@IT2015" \
    --wsdl-destination='/var/www/workspace/Fungku/' \
    --wsdl-prefix="Fungku" \
    --force