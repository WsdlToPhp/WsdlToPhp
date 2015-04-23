<?php

namespace WsdlToPhp\PackageGenerator\Generator;

use WsdlToPhp\PackageGenerator\Model\AbstractModel;
use WsdlToPhp\PackageGenerator\ConfigurationReader\GeneratorOptions;

class Utils
{
    /**
     * Gets category part
     * @param GeneratorOptions $options
     * @param AbstractModel $model the model for which we generate the folder
     * @param string $_optionName category type
     * @return string
     */
    public static function getPart(GeneratorOptions $options, AbstractModel $model, $_optionName)
    {
        $elementType = '';
        $optionValue = null;
        $string = $model->getCleanName();
        switch ($_optionName) {
            case GeneratorOptions::CATEGORY:
                $optionValue = $options->getCategory();
                break;
            case GeneratorOptions::SUB_CATEGORY:
                $optionValue = $options->getSubCategory();
                $mainCatPart = self::getPart($options, $model, GeneratorOptions::CATEGORY);
                switch ($options->getCategory()) {
                    case GeneratorOptions::VALUE_END:
                        if ($string != $mainCatPart && strlen($mainCatPart) < strlen($string)) {
                            $string = substr($string, 0, strlen($string) - strlen($mainCatPart));
                        } elseif ($string == $mainCatPart) {
                            $string = '';
                        }
                        break;
                    case GeneratorOptions::VALUE_START:
                        if ($string != $mainCatPart && strlen($mainCatPart) < strlen($string)) {
                            $string = substr($string, strlen($mainCatPart));
                        } elseif ($string == $mainCatPart) {
                            $string = '';
                        }
                        break;
                    default:
                        $string = '';
                        break;
                }
                break;
            case GeneratorOptions::GATHER_METHODS:
                $optionValue = $options->getGatherMethods();
                break;
        }
        if (!empty($string)) {
            switch ($optionValue) {
                case GeneratorOptions::VALUE_END:
                    $parts = preg_split('/[A-Z]/', ucfirst($string));
                    if (count($parts) == 0) {
                        $elementType = $string;
                    } elseif (!empty($parts[count($parts) - 1])) {
                        $elementType = substr($string, strrpos($string, implode('', array_slice($parts, -1))) - 1);
                    } else {
                        $part = '';
                        for ($i = count($parts) - 1; $i >= 0; $i--) {
                            $part = trim($parts[$i]);
                            if (!empty($part)) {
                                break;
                            }
                        }
                        $elementType = substr($string, ((count($parts) - 2 - $i) + 1) * -1);
                    }
                    break;
                case GeneratorOptions::VALUE_START:
                    $parts = preg_split('/[A-Z]/', ucfirst($string));
                    if (count($parts) == 0) {
                        $elementType = $string;
                    } elseif (empty($parts[0]) && !empty($parts[1])) {
                        $elementType = substr($string, 0, strlen($parts[1]) + 1);
                    } else {
                        $part = '';
                        for ($i = 0; $i < count($parts); $i++) {
                            $part = trim($parts[$i]);
                            if (!empty($part)) {
                                break;
                            }
                        }
                        $elementType = substr($string, 0, $i);
                    }
                    break;
                case GeneratorOptions::VALUE_CAT:
                    $elementType = $model->getContextualPart();
                    break;
                default:
                    break;
            }
        }
        return $elementType;
    }
    /**
     * Get content from url using a proxy or not
     * @param string $url
     * @param string $proxyHost
     * @param string $proxyPort
     * @param string $proxyLogin
     * @param string $proxyPassword
     * @return string
     */
    public static function getContentFromUrl($url, $proxyHost = null, $proxyPort = null, $proxyLogin = null, $proxyPassword = null)
    {
        $context = null;
        if (!empty($proxyHost)) {
            $protocol = strpos($proxyHost, 'https://') !== false ? 'https' : 'http';
            $options = array(
                $protocol => array(
                    'proxy' => sprintf('tcp://%s%s',
                        $proxyHost,
                        empty($proxyPort) ? '' : sprintf(':%s', $proxyPort)
                    ),
                    'header'=>array(
                        sprintf('Proxy-Authorization: Basic %s', base64_encode(sprintf(empty($proxyPassword) ? '%s@' : '%s:%s@', urlencode($proxyLogin), empty($proxyPassword) ? '' : urlencode($proxyPassword)))),
                    ),
                ),
            );
            $context = stream_context_create($options);
        }
        return file_get_contents($url, false, $context);
    }
    /**
     * Returns the value with good type
     * @param mixed $value the value
     * @return mixed
     */
    public static function getValueWithinItsType($value, $knownType = null)
    {
        if (is_int($value) || (!is_null($value) && in_array($knownType, array('time', 'positiveInteger', 'unsignedLong', 'unsignedInt', 'short', 'long', 'int', 'integer')))) {
            return intval($value);
        } elseif (is_float($value) || (!is_null($value) && in_array($knownType, array('float', 'double', 'decimal')))) {
            return floatval($value);
        } elseif (is_numeric($value)) {
            return intval($value) == $value ? intval($value) : floatval($value);
        } elseif (is_bool($value) || (!is_null($value) && in_array($knownType, array('bool', 'boolean')))) {
            return ($value === 'true' || $value === true || $value === 1 || $value === '1');
        } else {
            return $value;
        }
    }
}
