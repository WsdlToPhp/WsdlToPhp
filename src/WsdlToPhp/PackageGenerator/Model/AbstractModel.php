<?php

namespace WsdlToPhp\PackageGenerator\Model;

use WsdlToPhp\PackageGenerator\ConfigurationReader\ReservedKeywords;
use WsdlToPhp\PackageGenerator\Generator\Generator;

/**
 * Class AbstractModel defines the basic properties and methods to operations and structs extracted from the WSDL
 */
abstract class AbstractModel
{
    /**
     * Constant used to define the key to store documentation value in meta
     * @var string
     */
    const META_DOCUMENTATION = 'documentation';
    /**
     * Constant used to define the key to store from schema value in meta
     * @var string
     */
    const META_FROM_SCHEMA = 'from schema';
    /**
     * Original name od the element
     * @var string
     */
    private $name = '';
    /**
     * Values associated to the operation
     * @var array
     */
    private $meta = array();
    /**
     * Define the inheritance of a struct by the name of the parent struct or type
     * @var string
     */
    private $inheritance = '';
    /**
     * Store the object which owns the current model
     * @var AbstractModel
     */
    private $owner = null;
    /**
     * Store all the models generated
     * @var array
     */
    private static $models = array();
    /**
     * Replaced keywords time in order to generate unique new keyword
     * @var array
     */
    private static $replacedReservedPhpKeywords = array();
    /**
     * Unique name generated in order to ensure unique naming (for struct constructor and setters/getters even for different case attribute name whith same value)
     * @var array
     */
    private static $uniqueNames = array();
    /**
     * Main constructor
     * @uses AbstractModel::setName()
     * @uses AbstractModel::updateModels()
     * @param string $name the original name
     * @return AbstractModel
     */
    public function __construct($name)
    {
        $this->setName($name);
        self::updateModels($this);
    }
    /**
     * Returns comments for the element
     * @return array
     */
    public function getComment()
    {
        return array();
    }
    /**
     * Returns the comments for the file
     * @uses AbstractModel::getPackagedName()
     * @uses Generator::instance()->getOptionAddComments()
     * @uses AbstractModel::getDocSubPackages()
     * @uses Generator::getPackageName()
     * @return array
     */
    private function getFileComment()
    {
        $comments = array();
        array_push($comments, 'File for class ' . $this->getPackagedName());
        array_push($comments, '@package ' . Generator::getPackageName());
        if (count($this->getDocSubPackages())) {
            array_push($comments, '@subpackage ' . implode(',', $this->getDocSubPackages()));
        }
        if (count(Generator::instance()->getOptionAddComments())) {
            foreach (Generator::instance()->getOptionAddComments() as $tagName => $tagValue) {
                array_push($comments, "@$tagName $tagValue");
            }
        }
        return $comments;
    }
    /**
     * Returns the comments for the class
     * @uses AbstractModel::getPackagedName()
     * @uses Generator::instance()->getOptionAddComments()
     * @uses AbstractModel::getDocumentation()
     * @uses AbstractModel::addMetaComment()
     * @uses AbstractModel::getDocSubPackages()
     * @uses Struct::getIsStruct()
     * @uses Generator::getPackageName()
     * @return array
     */
    private function getClassComment()
    {
        $comments = array();
        array_push($comments, 'This class stands for ' . $this->getPackagedName() . ' originally named ' . $this->getName());
        if ($this->getDocumentation() != '')
            array_push($comments, 'Documentation : ' . $this->getDocumentation());
        $this->addMetaComment($comments, false, true);
        if ($this->getInheritance() != '') {
            $inheritedModel = self::getModelByName($this->getInheritance());
            /**
             * A virtual struct exists only to store meta informations about itself
             * So don't add meta informations about a valid struct
             */
            if ($inheritedModel && !$inheritedModel->getIsStruct()) {
                $inheritedModel->addMetaComment($comments, false, false);
            }
        }
        array_push($comments, '@package ' . Generator::getPackageName());
        if (count($this->getDocSubPackages())) {
            array_push($comments, '@subpackage ' . implode(',', $this->getDocSubPackages()));
        }
        if (count(Generator::instance()->getOptionAddComments())) {
            foreach (Generator::instance()->getOptionAddComments() as $tagName => $tagValue) {
                array_push($comments, "@$tagName $tagValue");
            }
        }
        return $comments;
    }
    /**
     * Method to override in sub class
     * Must return a string in order to declare the function, attribute or the value
     * @uses Struct::getIsStruct()
     * @uses AbstractModel::getModelByName()
     * @uses AbstractModel::getInheritance()
     * @uses AbstractModel::getComment()
     * @uses AbstractModel::getPackagedName()
     * @uses AbstractModel::getClassBody()
     * @uses AbstractModel::getGenericWsdlClassName()
     * @uses Generator::instance()->getOptionInheritsClassIdentifier()
     * @uses Generator::instance()->getOptionGenerateWsdlClassFile()
     * @return string
     */
    public function getClassDeclaration()
    {
        $class = array();
        /**
         * Class comments
         */
        array_push($class, array('comment' => $this->getFileComment()));
        array_push($class, array('comment' => $this->getClassComment()));
        /**
         * Extends
         */
        $extends = '';
        $base = Generator::instance()->getOptionInheritsClassIdentifier();
        if (!empty($base) && ($model = self::getModelByName($this->getName() . $base))) {
            if ($model->getIsStruct()) {
                $extends = $model->getPackagedName();
            }
        } elseif ($this->getInheritance() != '' && ($model = self::getModelByName($this->getInheritance()))) {
            if ($model->getIsStruct()) {
                $extends = $model->getPackagedName();
            }
        } elseif (class_exists($this->getInheritance()) && stripos($this->getInheritance(), Generator::getPackageName()) === 0) {
            $extends = $this->getInheritance();
        }
        if (empty($extends) && Generator::instance()->getOptionGenerateWsdlClassFile()) {
            $extends = self::getGenericWsdlClassName();
        }
        array_push($class, 'class ' . $this->getPackagedName() . (!empty($extends) ? ' extends ' . $extends : ''));
        /**
         * Class body starts here
         */
        array_push($class, '{');
        /**
         * Populate class body
         */
        $this->getClassBody($class);
        /**
         * __toString() method comments
         */
        $comments = array();
        array_push($comments, 'Method returning the class name');
        array_push($comments, '@return string __CLASS__');
        array_push($class, array('comment' => $comments));
        unset($comments);
        /**
         * __toString method body
         */
        array_push($class, 'public function __toString()');
        array_push($class, '{');
        array_push($class, 'return __CLASS__;');
        array_push($class, '}');
        /**
         * Class body ends here
         */
        array_push($class, '}');
        return $class;
    }
    /**
     * Methods which fills the class body
     * Must be overridden in classes
     * @param array
     * @return void
     */
    abstract public function getClassBody(&$class);
    /**
     * Returns the name of the class the current class inherits from
     * @return string
     */
    public function getInheritance()
    {
        return $this->inheritance;
    }
    /**
     * Sets the name of the class the current class inherits from
     * @uses AbstractModel::updateModels()
     * @param string
     */
    public function setInheritance($inheritance = '')
    {
        $this->inheritance = $inheritance;
        self::updateModels($this);
        return $inheritance;
    }
    /**
     * Add meta informations to comment array
     * @uses AbstractModel::META_DOCUMENTATION
     * @uses AbstractModel::getMeta()
     * @uses AbstractModel::cleanComment()
     * @param array $comments array which meta are added to
     * @param bool $addStars add comments tags
     * @param bool $ignoreDocumentation ignore documentation info or not
     * @return void
     */
    protected function addMetaComment(array &$comments = array(), $addStars = false, $ignoreDocumentation = false)
    {
        $metaComments = array();
        if (count($this->getMeta())) {
            foreach ($this->getMeta() as $metaName => $metaValue) {
                $cleanedMetaValue = self::cleanComment($metaValue, $metaName == self::META_DOCUMENTATION ? ' ' : ',', stripos($metaName, 'SOAPHeader') === false);
                if (($ignoreDocumentation && $metaName == self::META_DOCUMENTATION) || $cleanedMetaValue === '')
                    continue;
                array_push($metaComments, ($addStars ? ' * ' : '') . "    - $metaName : " . (($metaName == self::META_FROM_SCHEMA && stripos($cleanedMetaValue, 'http') === 0) ? "{@link $cleanedMetaValue}" : $cleanedMetaValue));
            }
        }
        if (count($metaComments)) {
            if (!in_array('Meta informations extracted from the WSDL', $comments))
                array_push($comments, 'Meta informations extracted from the WSDL');
            foreach ($metaComments as $metaComment)
                array_push($comments, $metaComment);
        }
        unset($metaComments);
    }
    /**
     * Returns the meta
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }
    /**
     * Sets the meta
     * @param array $meta
     * @return array
     */
    public function setMeta(array $meta = array())
    {
        return ($this->meta = $meta);
    }
    /**
     * Add meta information to the operation
     * @uses AbstractModel::getMeta()
     * @uses AbstractModel::updateModels()
     * @param string $metaName
     * @param mixed $metaValue
     * @return AbstractModel
     */
    public function addMeta($metaName, $metaValue)
    {
        if (!is_scalar($metaName) || (!is_scalar($metaValue) && !is_array($metaValue))) {
            return '';
        }
        $metaValue = is_scalar($metaValue) ? trim($metaValue) : $metaValue;
        if (is_scalar($metaValue) && $metaValue === '') {
            return false;
        }
        if (!array_key_exists($metaName, $this->getMeta())) {
            $this->meta[$metaName] = $metaValue;
        } elseif (is_array($this->meta[$metaName]) && is_array($metaValue)) {
            $this->meta[$metaName] = array_merge($this->meta[$metaName], $metaValue);
        } elseif (is_array($this->meta[$metaName])) {
            array_push($this->meta[$metaName], $metaValue);
        } else {
            $this->meta[$metaName] = $metaValue;
        }
        ksort($this->meta);
        self::updateModels($this);
        return $this;
    }
    /**
     * Sets the documentation meta value.
     * Documentation is set as an array so if multiple documentation nodes are set for an unique element, it will gather them.
     * @uses AbstractModel::META_DOCUMENTATION
     * @uses AbstractModel::addMeta()
     * @param string $documentation the documentation from the WSDL
     * @return AbstractModel
     */
    public function setDocumentation($documentation)
    {
        return $this->addMeta(self::META_DOCUMENTATION, is_array($documentation) ? $documentation : array($documentation));
    }
    /**
     * Get the documentation meta value
     * @uses AbstractModel::META_DOCUMENTATION
     * @uses AbstractModel::getMetaValue()
     * @uses AbstractModel::cleanComment()
     * @return string the documentation from the WSDL
     */
    public function getDocumentation()
    {
        return self::cleanComment($this->getMetaValue(self::META_DOCUMENTATION, ''), ' ');
    }
    /**
     * Sets the from schema meta value.
     * @uses AbstractModel::META_FROM_SCHEMA
     * @uses AbstractModel::addMeta()
     * @param string $fromSchema the url from which the element comes from
     * @return AbstractModel
     */
    public function setFromSchema($fromSchema)
    {
        return $this->addMeta(self::META_FROM_SCHEMA, $fromSchema);
    }
    /**
     * Get the from schema meta value
     * @uses AbstractModel::META_FROM_SCHEMA
     * @uses AbstractModel::getMetaValue()
     * @return string the from schema meta value
     */
    public function getFromSchema()
    {
        return $this->getMetaValue(self::META_FROM_SCHEMA, '');
    }
    /**
     * Returns a meta value according to its name
     * @uses AbstractModel::getMeta()
     * @param string $metaName the meta information name
     * @param string $fallback the fallback value if unset
     * @return mixed the meta information value
     */
    public function getMetaValue($metaName, $fallback = null)
    {
        return array_key_exists($metaName, $this->getMeta()) ? $this->meta[$metaName] : $fallback;
    }
    /**
     * Returns the value of the first meta value assigned to the name
     * @param array $names the meta names to check
     * @param string $fallback the fallback value if anyone is set
     * @return mixed the meta information value
     */
    public function getMetaValueFirstSet(array $names, $fallback = null)
    {
        foreach ($names as $name) {
            if (array_key_exists($name, $this->getMeta())) {
                return $this->meta[$name];
            }
        }
        return $fallback;
    }
    /**
     * Returns the original name extracted from the WSDL
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Sets the original name extracted from the WSDL
     * @param string $name
     * @return string
     */
    public function setName($name)
    {
        return ($this->name = $name);
    }
    /**
     * Returns a valid clean name for PHP
     * @uses AbstractModel::getName()
     * @uses AbstractModel::cleanString()
     * @param bool $keepMultipleUnderscores optional, allows to keep the multiple consecutive underscores
     * @return string
     */
    public function getCleanName($keepMultipleUnderscores = true)
    {
        return self::cleanString($this->getName(), $keepMultipleUnderscores);
    }
    /**
     * Returns the owner model object
     * @return AbstractModel
     */
    public function getOwner()
    {
        return $this->owner;
    }
    /**
     * Sets the owner model object
     * @param AbstractModel $owner object the owner of the current model
     * @uses AbstractModel::updateModels()
     * @return AbstractModel
     */
    public function setOwner(AbstractModel $owner)
    {
        $this->owner = $owner;
        self::updateModels($this);
        return $owner;
    }
    /**
     * Returns true if the original name is safe to use as a PHP property, variable name or class name
     * @uses AbstractModel::getName()
     * @uses AbstractModel::getCleanName()
     * @return bool
     */
    public function nameIsClean()
    {
        return ($this->getName() != '' && $this->getName() == $this->getCleanName());
    }
    /**
     * Returns the packaged name
     * @uses Generator::getPackageName()
     * @uses AbstractModel::getCleanName()
     * @uses AbstractModel::getContextualPart()
     * @uses AbstractModel::uniqueName() to ensure unique naming of struct case sensitively
     * @return string
     */
    public function getPackagedName()
    {
        return Generator::getPackageName() . $this->getContextualPart() . ucfirst(self::uniqueName($this->getCleanName(), $this->getContextualPart()));
    }
    /**
     * Allows to define the contextual part of the class name for the package
     * @return string
     */
    public function getContextualPart()
    {
        return '';
    }
    /**
     * Returns the sub package name which the model belongs to
     * Must be overridden by sub classes
     * @return array
     */
    public function getDocSubPackages()
    {
        return array();
    }
    /**
     * Clean a string to make it valid as PHP variable
     * @param string $string the string to clean
     * @param bool $keepMultipleUnderscores optional, allows to keep the multiple consecutive underscores
     * @return string
     */
    public static function cleanString($string, $keepMultipleUnderscores = true)
    {
        $cleanedString = preg_replace('/[^a-zA-Z0-9_]/', '_', $string);
        if (!$keepMultipleUnderscores) {
            $cleanedString = preg_replace('/[_]+/', '_', $cleanedString);
        }
        return $cleanedString;
    }
    /**
     * Get models
     * @return array
     */
    public static function getModels()
    {
        return self::$models;
    }
    /**
     * Returns the model by its name
     * @uses AbstractModel::getModels()
     * @param string $modelName the original Struct name
     * @return Struct|null
     */
    public static function getModelByName($modelName)
    {
        if (!is_scalar($modelName)) {
            return null;
        }
        return array_key_exists('_' . $modelName . '_', self::getModels()) ? self::$models['_' . $modelName . '_'] : null;
    }
    /**
     * Updates models with model
     * @uses AbstractModel::getName()
     * @uses AbstractModel::__toString()
     * @param AbstractModel $model a AbstractModel object
     * @return Struct|bool
     */
    protected static function updateModels(AbstractModel $model)
    {
        if ($model->__toString() != 'Struct' || !$model->getName()) {
            return false;
        }
        return (self::$models['_' . $model->getName() . '_'] = $model);
    }
    /**
     * Returns a usable keyword for a original keyword
     * @param string $keyword the keyword
     * @param string $context the context
     * @return string
     */
    public static function replaceReservedPhpKeyword($keyword, $context)
    {
        $phpReservedKeywordFound = '';
        if (ReservedKeywords::instance()->is($keyword)) {
            $keywordKey = $phpReservedKeywordFound . '_' . $context;
            if (!array_key_exists($keywordKey, self::$replacedReservedPhpKeywords)) {
                self::$replacedReservedPhpKeywords[$keywordKey] = 0;
            } else {
                self::$replacedReservedPhpKeywords[$keywordKey]++;
            }
            return '_' . $keyword . (self::$replacedReservedPhpKeywords[$keywordKey] ? '_' . self::$replacedReservedPhpKeywords[$keywordKey] : '');
        } else {
            return $keyword;
        }
    }
    /**
     * Static method wich returns a unique name case sensitively
     * Useful to name methods case sensitively distinct, see http://the-echoplex.net/log/php-case-sensitivity
     * @param string $name the original name
     * @param string $context the context where the name is needed unique
     * @return string
     */
    protected static function uniqueName($name, $context)
    {
        $insensitiveKey = strtolower($name . '_' . $context);
        $sensitiveKey = $name . '_' . $context;
        if (array_key_exists($sensitiveKey, self::$uniqueNames)) {
            return self::$uniqueNames[$sensitiveKey];
        } elseif (!array_key_exists($insensitiveKey, self::$uniqueNames)) {
            self::$uniqueNames[$insensitiveKey] = 0;
        } else {
            self::$uniqueNames[$insensitiveKey]++;
        }
        $uniqueName = $name . (self::$uniqueNames[$insensitiveKey] ? '_' . self::$uniqueNames[$insensitiveKey] : '');
        self::$uniqueNames[$sensitiveKey] = $uniqueName;
        return $uniqueName;
    }
    /**
     * Clean comment
     * @param string $comment the comment to clean
     * @param string $glueSeparator ths string to use when gathering values
     * @param bool $uniqueValues indicates if comment values must be unique or not
     * @return string
     */
    public static function cleanComment($comment, $glueSeparator = ',', $uniqueValues = true)
    {
        if (!is_scalar($comment) && !is_array($comment)) {
            return '';
        }
        return trim(str_replace('*/', '*[:slash:]', is_scalar($comment) ? $comment : implode($glueSeparator, $uniqueValues ? array_unique($comment) : $comment)));
    }
    /**
     * Returns the generic name of the WsdlClass
     * @uses Generator::getPackageName()
     * @return string
     */
    public static function getGenericWsdlClassName()
    {
        return Generator::getPackageName() . 'WsdlClass';
    }
    /**
     * Returns class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return 'AbstractModel';
    }
}
