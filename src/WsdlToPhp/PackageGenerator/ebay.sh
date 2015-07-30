rm -rf /var/www/workspace/Api/*.php \
        /var/www/workspace/Api/Enum* \
        /var/www/workspace/Api/Struct* \
        /var/www/workspace/Api/Service* \
        /var/www/workspace/Api/Array*;

php console wsdltophp:generate:package \
        --wsdl-urlorpath="Tests/resources/ebaySvc.wsdl" \
        --wsdl-proxy-host="****************************" \
        --wsdl-proxy-port=*******  \
        --wsdl-proxy-login="*******" \
        --wsdl-proxy-password="*******" \
        --wsdl-destination='/var/www/Api/' \
        --wsdl-prefix="Api" \
        --wsdl-category="cat" \
        --wsdl-gathermethods="start" \
        --wsdl-genericconstants=false \
        --wsdl-gentutorial=true \
        --wsdl-namespace="My\Project" \
        --wsdl-standalone=true \
        --wsdl-addcomments="date:2015-04-22" \
        --wsdl-addcomments="author:Me" \
        --wsdl-addcomments="release:1.1.0" \
        --wsdl-addcomments="team:Dream" \
        --wsdl-namespace="My\Project" \