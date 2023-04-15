<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use LFreeze\PackagePrivate\PackagePrivate;
use LFreeze\PackagePrivate\AssignAttribute;
use LFreeze\PackagePrivate\Exception\CallerNamespaceUndefinedException;
use LFreeze\PackagePrivate\Exception\UninitializedException;
use UnexpectedValueException;
use Tests\Unit\NamespaceFirst;
use Tests\Unit\NamespaceSecond;
use ReflectionClass;
use TypeError;
use BadMethodCallException;

/**
 * @covers LFreeze\PackagePrivate\AssignAttribute
 */
class PackagePrivateAttributeTest extends TestCase
{

    public function test_assignCallerNamespaceを呼ばずにプロパティにアクセスしたらエラー_getter() {
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->packagePrivateInt;
            }
        };
        $this->expectException(CallerNamespaceUndefinedException::class);
        $testClass->do();
    }

    public function test_assignCallerNamespaceを呼ばずにプロパティにアクセスしたらエラー_setter() {
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->packagePrivateInt = 132;
            }
        };
        $this->expectException(CallerNamespaceUndefinedException::class);
        $testClass->do();
    }

    public function test_createを通さずインスタンスを使ったらプロパティアクセス時にエラー() {
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = new NamespaceFirst\Callee();
                $callee->packagePrivateInt = 132;
            }
        };
        $this->expectException(UninitializedException::class);
        $testClass->do();
    }

    public function test_namespaceが異なる場合プロパティアクセス時にエラー() {
        $testClass = new class extends NamespaceSecond\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->packagePrivateInt = 132;
            }
        };
        $this->expectException(UnexpectedValueException::class);
        $testClass->do();
    }

    public function test_PackagePrivate属性の無いプロパティにアクセスしたらエラー_getter() {
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->privateInt;
            }
        };
        $this->expectException(UnexpectedValueException::class);
        $testClass->do();
    }

    public function test_PackagePrivate属性の無いプロパティにアクセスしたらエラー_setter() {
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->privateInt = 123;
            }
        };
        $this->expectException(UnexpectedValueException::class);
        $testClass->do();
    }

    public function test_PackagePrivate属性のあるプロパティにアクセスできる() {
        $expect = 31415926435;
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->packagePrivateInt = 31415926435;
                return $callee->packagePrivateInt;
            }
        };
        $this->assertSame($expect, $testClass->do());
    }

    public function test_PackagePrivate属性のあるプロパティにアクセスできるが型が合わない場合エラー() {
        $expect = 31415926435;
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->packagePrivateString = 31415926435;
                return $callee->packagePrivateString;
            }
        };
        $this->expectException(TypeError::class);
        $testClass->do();
    }

    public function test_呼び出し先クラスにないプロパティへのアクセスはできない() {
        $expect = 31415926435;
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->newProperty = 31415926435;
                return $callee->newProperty;
            }
        };
        $this->expectException(UnexpectedValueException::class);
        $testClass->do();
    }
    
    public function test_PackagePrivate属性の無いprivateメソッドにアクセスしたらエラー() {
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->privateMethod();
            }
        };
        $this->expectException(BadMethodCallException::class);
        $testClass->do();
    }
    
    public function test_PackagePrivate属性の無いpublicメソッドにアクセスできる() {
        $expect = 'publicMethod';
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                return $callee->publicMethod();
                
            }
        };
        $this->assertSame($expect, $testClass->do());
    }
    
    public function test_存在しないメソッドへのアクセスでエラー() {
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                $callee->undefinedMethod();
            }
        };
        $this->expectException(BadMethodCallException::class);
        $testClass->do();
    }
    
    public function test_PackagePrivate属性のあるprivateメソッドにアクセスできる() {
        $expect = 'packagePrivateMethod';
        $testClass = new class extends NamespaceFirst\Caller {
            public function do() {
                $callerNamespace = (new ReflectionClass($this))->getNamespaceName();
                NamespaceFirst\Callee::assignCallerNamespaceName($callerNamespace);
                $callee = (new NamespaceFirst\Callee())->__create();
                return $callee->packagePrivateMethod();
            }
        };
        $this->assertSame($expect, $testClass->do());
    }
}




