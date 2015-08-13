clear;

rm -rf /var/www/workspace/TedWichman/Api/Enum /var/www/workspace/TedWichman/Api/Service /var/www/workspace/TedWichman/Api/Struct  /var/www/workspace/TedWichman/Api/*.php;
 
php console wsdltophp:generate:package \
    --wsdl-urlorpath="/var/www/workspace/TedWichman/gowireless.wsdl" \
    --wsdl-proxy-host="nrs-proxy02.ad-subs.w2k.francetelecom.fr" \
    --wsdl-proxy-port=3128 \
    --wsdl-proxy-login="fzcd1760" \
    --wsdl-proxy-password="losled@IT2015" \
    --wsdl-destination='/var/www/workspace/TedWichman/Api/' \
    --wsdl-prefix="GoWireless" \
    --force