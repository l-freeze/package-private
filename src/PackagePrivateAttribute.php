<?php
declare(strict_types=1);

namespace LFreeze\PackagePrivate;

use LFreeze\PackagePrivate\Exception\CallerNamespaceUndefinedException;
use LFreeze\PackagePrivate\Exception\UninitializedException;
use ReflectionClass;
use ReflectionProperty;
use BadMethodCallException;
use ReflectionParameter;
use UnexpectedValueException;

trait PackagePrivateAttribute {

    private const ATTRIBUTE_NAME = 'PackagePrivate';
    private const ATTRIBUTE_ARGUMENTS_NAME = 'allowedNamespaces';

    private bool $_initialized = false;
    static private $_callersNamespaceName = '';
    private bool $_isApplicable = false;

    /**
     * @var array<string> $_allowedNamespaces;
     */
    readonly private array $_allowedNamespaces;
    readonly private string $_caller;
    readonly private array $_packagePrivateProperties;
    readonly private array $_packagePrivateMethods;

    static public function assignCallerNamespaceName(string $namespace) {
        static::$_callersNamespaceName = $namespace;
    }

    /**
     * PackagePrivate属性を[有効化|無効化]したインスタンスを返す
     *
     * @param bool $args コンストラクタを呼び出す時の可変長引数
     * @return self
     */
    public function __create(bool $packagePrivateEnabled = true): self {
        return match($packagePrivateEnabled) {
            true => $this->packagePrivateEnabled(),
            false => $this->packagePrivateDisabled(),
        };
    }

    private function packagePrivateEnabled() {
        $self = new $this;
        $reflectionClass = new ReflectionClass($self);

        $eraseCharCount = strlen(__NAMESPACE__) + 1;

        //class
        $classAttribute = array_filter(
            $reflectionClass->getAttributes(), 
            fn($attribute)=> $attribute->getName() === __NAMESPACE__.'\\'.static::ATTRIBUTE_NAME
        )[0] ?? null;

        /**
         * packageprivateの適用対象のnamespace配列の生成
         * 
         * PackagePrivate(namespaces:array)で指定されている場合はnamespaces、
         * 指定されていない場合はPackagePrivateを適用しているクラスのnamespace単体
         */
        $applicableNamespaces = []; //
        if (!is_null($classAttribute)) {
            $applicableNamespaces = array_filter(
                $classAttribute->getArguments(),
                fn($v, string $k) => $k===static::ATTRIBUTE_ARGUMENTS_NAME,
                ARRAY_FILTER_USE_BOTH
            )[static::ATTRIBUTE_ARGUMENTS_NAME] ?? [$reflectionClass->getNamespaceName()];
        } else {
            $applicableNamespaces = [$reflectionClass->getNamespaceName()];
        }
        
        //fields
        $properties = [];
        $reflectionProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE);
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

        //constructor property promotion
        $reflectionConstructorParameters = $reflectionClass->getConstructor()->getParameters();
        foreach($reflectionConstructorParameters as $constructorParameters) {
            $attributes = $constructorParameters->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeName = $attribute->getName();
                if (substr($attributeName, $eraseCharCount, strlen($attributeName) - $eraseCharCount) === static::ATTRIBUTE_NAME) {
                    $properties[] = $reflectionProperty->getName();
                    continue;
                }
            }
        }

        //methods
        $methods = [];
        $reflectionMethods = $reflectionClass->getMethods(ReflectionProperty::IS_PRIVATE);
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

        $constructorNamedArgumentsNames = array_map(
            fn(ReflectionParameter $parameter)=> $parameter->getName(), 
            $reflectionClass->getConstructor()->getParameters()
        );
        $args = [];
        foreach($constructorNamedArgumentsNames as $argumentsName) {
            $args[$argumentsName] = $this->$argumentsName;
        }
        $instance = $reflectionClass->newInstance(...$args);
        $instance->_packagePrivateProperties = $properties;
        $instance->_packagePrivateMethods = $methods;
        $instance->_caller = static::$_callersNamespaceName;
        $instance->_allowedNamespaces = $applicableNamespaces;
        $instance->_initialized = true;
        return $instance;
    }

    private function packagePrivateDisabled() {
        $self = new $this;
        $reflectionClass = new ReflectionClass($self);

        $constructorNamedArgumentsNames = array_map(fn(ReflectionParameter $parameter)=> $parameter->getName(), $reflectionClass->getConstructor()->getParameters());
        $args = [];
        foreach($constructorNamedArgumentsNames as $argumentsName) {
            $args[$argumentsName] = $this->$argumentsName;
        }
        $instance = $reflectionClass->newInstance(...$args);
        $instance->_packagePrivateProperties = [];
        $instance->_packagePrivateMethods = [];
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

        if ($this->__checkApplicable()) {
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
        
        if ($this->__checkApplicable()) {
            if (in_array($property, $this->_packagePrivateProperties)) {
                return $this->$property;
            }
        }

        throw new UnexpectedValueException(static::createPropertyAccessErrorMessage(__CLASS__, $property));
        //static::throwPropertyAccessFatalError(__CLASS__, $property);
    }

    public function __call($method, array $arguments) {
        if ($this->__checkApplicable()) {
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

    private function __checkApplicable(): bool {
        if ($this->_isApplicable) {
            return true;
        }
        foreach($this->_allowedNamespaces as $allow) {
            if ($this->_caller === $allow) {
                $this->_isApplicable = true;
                break;
            }

            $pattern = '#^'.addslashes($allow.'\\') .'.*#';
            if (preg_match($pattern, self::$_callersNamespaceName.'\\') === 1) {
                $this->_isApplicable = true;
                break;
            }
        }
        return $this->_isApplicable;
    }

}