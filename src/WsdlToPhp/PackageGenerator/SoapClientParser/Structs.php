<?php

namespace WsdlToPhp\PackageGenerator\SoapClientParser;

class Structs extends AbstractParser
{
    public function parse()
    {
        $types   = $this->generator->__getTypes();
        $structs = $this->generator->getStructs();
        if (is_array($types) && count($types)) {
            $structsDefined = array();
            foreach ($types as $type) {
                $typeSignature = md5($type);
                /**
                 * Remove useless break line, tabs
                 */
                $type = str_replace("\r", '', $type);
                $type = str_replace("\n", '', $type);
                $type = str_replace("\t", '', $type);
                /**
                 * Remove curly braces
                 */
                $type = str_replace("{", '', $type);
                $type = str_replace("}", '', $type);
                /**
                 * Remove brackets
                 */
                $type = str_replace("[", '', $type);
                $type = str_replace("]", '', $type);
                /**
                 * Adds space to parse it
                 */
                $type = str_replace(';', ' ;', $type);
                /**
                 * Remove duplicate spaces
                 */
                $type = preg_replace('/[\s]+/', ' ', $type);
                /**
                 * Explode definition based on format :
                 * struct {struct_name} {paramName} {paramValue} ;[{paramName} {paramValue} ;]+
                 */
                $typeDef = explode(' ', $type);
                /**
                 * Gets struct definition start
                 */
                $struct = $typeDef[0];
                if ($struct != 'struct') {
                    if (! empty($typeDef[1])) {
                        $structs->addVirtualStruct($typeDef[1]);
                    }
                    continue;
                }
                /**
                 * Catch struct name
                 */
                $structName = $typeDef[1];
                /**
                 * Struct already known? If not, then parse it and add attributes to it.
                 * We don't parse twice the same struct.
                 * This test now lets pass identically named elements with different structure such as the two followings:
                 * - struct Create { Create request; }
                 * - struct Create { ArrayOfDetailItem Details; string UserID; string Password; string TestMode; etc. }
                 * This will generate a Struct class containing the merge of all the different structures
                 */
                if (in_array($typeSignature, $structsDefined)) {
                    continue;
                }
                /**
                 * Collect struct params
                 */
                $start = false;
                $then = false;
                $end = false;
                $structParamName = '';
                $structParamType = '';
                $typeDefCount = count($typeDef);
                if ($typeDefCount > 3) {
                    for ($i = 2; $i < $typeDefCount; $i ++) {
                        $typeVal = $typeDef[$i];
                        if ($typeVal != '{' && is_string($typeVal) && ! empty($typeVal) && ! $start) {
                            $end = false;
                            $then = false;
                            $start = true;
                        }
                        if ($typeVal === ';') {
                            $end = true;
                            $then = false;
                            $start = false;
                        }
                        if ($then) {
                            $structParamName = $typeVal;
                            if (! empty($structParamType) && ! empty($structParamName) && ! empty($structName)) {
                                $structs->addStruct($structName, $structParamName, $structParamType);
                                array_push($structsDefined, $typeSignature);
                                $structParamName = '';
                                $structParamType = '';
                            }
                        }
                        if ($start && ! $then) {
                            /**
                             * Replace some weird definition to known valid type
                             */
                            $typeVal = str_replace('<anyXML>', '\DOMDocument', $typeVal);
                            $structParamType = $typeVal;
                            $then = true;
                        }
                    }
                } else {
                    $structs->addStruct($structName, $structParamName, $structParamType);
                }
            }
        }
    }
}
