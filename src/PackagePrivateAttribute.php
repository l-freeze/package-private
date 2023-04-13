<?php
declare(strict_types=1);

namespace LFreeze\PackagePrivate;

use LFreeze\PackagePrivate\Exception\CallerNamespaceUndefinedException;
use LFreeze\PackagePrivate\Exception\UninitializedException;
use ReflectionClass;
use ReflectionProperty;
use BadMethodCallException;
use UnexpectedValueException;

trait PackagePrivateAttribute {

    private bool $_initialized = false;
    static private $_callersNamespaceName = '';
    private const ATTRIBUTE_NAME = 'PackagePrivate';

    readonly private string $_packageName;
    readonly private array $_packagePrivateProperties;
    readonly private array $_packagePrivateMethods;

    static public function assignCallerNamespaceName(string $namespace) {
        static::$_callersNamespaceName = $namespace;
    }

    /**
     * PackagePrivate属性を有効化したインスタンスを返す
     *
     * @param mixed $args コンストラクタを呼び出す時の可変長引数
     * @return self
     */
    static public function create(...$args): self {
        $self = new self;
        $reflectionClass = new ReflectionClass($self);

        $eraseCharCount = strlen(__NAMESPACE__) + 1;

        //fields
        $reflectionProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);
        $properties = [];
        foreach ($reflectionProperties as $reflectionProperty) {
            $attributes = $reflectionProperty->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeName = $attribute->getName();
                if (substr($attributeName, $eraseCharCount, strlen($attributeName) - $eraseCharCount) === static::ATTRIBUTE_NAME) {
                    $properties[] = $reflectionProperty->getName();
                    continue;
                }
            }
        }

        //methods
        $reflectionMethods = $reflectionClass->getMethods(ReflectionProperty::IS_PRIVATE);
        $methods = [];
        foreach ($reflectionMethods as $reflectionMethod) {
            $attributes = $reflectionMethod->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeName = $attribute->getName();
                if (substr($attributeName, $eraseCharCount, strlen($attributeName) - $eraseCharCount) === static::ATTRIBUTE_NAME) {
                    $methods[] = $reflectionMethod->getName();
                    continue;
                }
            }
        }

        $instance = $reflectionClass->newInstance(...$args);
        $instance->_packagePrivateProperties = $properties;
        $instance->_packagePrivateMethods = $methods;
        $instance->_packageName = static::$_callersNamespaceName;
        $instance->_initialized = true;
        return $instance;
    }

    public function __set($property, $v) {
        if (self::$_callersNamespaceName === '') {
            throw new CallerNamespaceUndefinedException('Must be call '.__CLASS__.'::assignCallerNamespaceName(__NAMESPACE__)');
        }

        if (!$this->_initialized) {
            throw new UninitializedException("Must be call create()");
        }

        if ((new ReflectionClass ($this))->getNamespaceName() ===  $this->_packageName) {
            if (in_array($property, $this->_packagePrivateProperties)) {
                $this->$property = $v;
                return;
            }
        }
        throw new UnexpectedValueException(static::createPropertyAccessErrorMessage(__CLASS__, $property));
        //static::throwPropertyAccessFatalError(__CLASS__, $property);
    }
    public function __get($property) {
        if (self::$_callersNamespaceName === '') {
            throw new CallerNamespaceUndefinedException('Must be call '.__CLASS__.'::assignCallerNamespaceName(__NAMESPACE__)');
        }

        if (!$this->_initialized) {
            throw new UninitializedException("Must be call create()");
        }
        
        if ((new ReflectionClass ($this))->getNamespaceName() ===  $this->_packageName) {
            if (in_array($property, $this->_packagePrivateProperties)) {
                return $this->$property;
            }
        }

        throw new UnexpectedValueException(static::createPropertyAccessErrorMessage(__CLASS__, $property));
        //static::throwPropertyAccessFatalError(__CLASS__, $property);
    }

    public function __call($method, array $arguments) {
        if ((new ReflectionClass ($this))->getNamespaceName() ===  $this->_packageName) {
            if (in_array($method, $this->_packagePrivateMethods)) {
                if (method_exists($this, $method)){
                    return $this->$method(...$arguments);
                }
            }
        }
        throw new BadMethodCallException(static::createMethodCallErrorMessage(__CLASS__, $method));
        //static::throwMethodCallFatalError(__CLASS__, $method);
    }

    static private function createPropertyAccessErrorMessage($namespace, $property){
        return 'Cannot access private property '.$namespace . '::$' . $property;
    }
    
    //static private function throwPropertyAccessFatalError($namespace, $property) {
    //    trigger_error(static::createPropertyAccessErrorMessage($namespace, $property), E_USER_ERROR);
    //}

    static private function createMethodCallErrorMessage($namespace, $method) {
        return 'Call to private method '.$namespace . '::' . $method . '()';
    }

    //static private function throwMethodCallFatalError($namespace, $method) {
    //    trigger_error(static::createMethodCallErrorMessage($namespace, $method), E_USER_ERROR);
    //}
}