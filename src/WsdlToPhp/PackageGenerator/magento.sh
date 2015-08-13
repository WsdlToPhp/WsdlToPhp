clear;

rm -rf  /var/www/workspace/Api/Enum* \
        /var/www/workspace/Api/Array* \
        /var/www/workspace/Api/Service* \
        /var/www/workspace/Api/Struct* \
        /var/www/workspace/Api/*.php;

php console wsdltophp:generate:package \
    --wsdl-urlorpath="http://demo.magentocommerce.com/api/v2_soap?wsdl=1" \
    --wsdl-proxy-host="nrs-proxy02.ad-subs.w2k.francetelecom.fr" \
    --wsdl-proxy-port=3128 \
    --wsdl-proxy-login="fzcd1760" \
    --wsdl-proxy-password="losled@IT2015" \
    --wsdl-destination='/var/www/workspace/Api/' \
    --wsdl-prefix="Api" \
    --wsdl-category="cat" \
    --wsdl-subcategory="" \
    --wsdl-gathermethods="start" \
    --wsdl-reponseasobj=false \
    --wsdl-sendarrayparam=false \
    --wsdl-genautoload=true \
    --wsdl-paramsasarray=false \
    --wsdl-inherits="" \
    --wsdl-genericconstants=false \
    --wsdl-gentutorial=true \
    --wsdl-addcomments="release:1.1.0" \
    --wsdl-namespace="My\Project" \
    --force