<?php
declare(strict_types=1);
namespace Example\NamespaceA;

use LFreeze\PackagePrivate\PackagePrivate;
use LFreeze\PackagePrivate\PackagePrivateAttribute;

final class Callee {
    use PackagePrivateAttribute;

    #[PackagePrivate]
    private int $packagePrivateInt = 9876543210;

    #[PackagePrivate]
    private string $packagePrivateString = "DefaultPrivateString";

    private int $privateInt = 123;

    public function __construct(public $namedArguments1 = 'DefaultPropertyX', public $namedArguments2 = 'DefaultPropertyY') {
    }

    #[PackagePrivate]
    private function packagePrivateMethod(?string $param = null, ?string $param2 = null) {
        return __FUNCTION__ . ($param ?? '') . ($param2 ?? '');
    }

    private function privateMethod(?string $param = null) {
        return __FUNCTION__ . ($param ?? '');
    }

    public function publicMethod(?string $param = null) {
        return __FUNCTION__ . ($param ?? '');
    }

    public function main(){
        $proc01 = $this->proc01(a: $this->packagePrivateString, b: $this->packagePrivateInt);
        $proc02 = $this->proc02(n: $this->packagePrivateInt);
        return $proc01.'@'.$proc02;
    }

    private function proc01(string $a, int $b){
        return '[Proc01] ($a, $b) = ('.$a .' , '. $b.')';
    }

    private function proc02(int $n){
        return $n+100;
    }

}