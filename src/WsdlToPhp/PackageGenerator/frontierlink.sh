clear;

rm -rf /var/www/workspace/FrontierLink/Enum /var/www/workspace/FrontierLink/Service /var/www/workspace/FrontierLink/Struct  /var/www/workspace/FrontierLink/*.php;
 
php console wsdltophp:generate:package \
    --wsdl-urlorpath="/var/www/workspace/aapt/aapt-v2/FrontierLink.v2.2.wsdl" \
    --wsdl-proxy-host="nrs-proxy02.ad-subs.w2k.francetelecom.fr" \
    --wsdl-proxy-port=3128 \
    --wsdl-proxy-login="fzcd1760" \
    --wsdl-proxy-password="losled@IT2015" \
    --wsdl-destination='/var/www/workspace/FrontierLink/' \
    --wsdl-prefix="FrontierLink" \
    --force