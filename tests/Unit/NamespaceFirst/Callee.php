<?php
declare(strict_types=1);

namespace Tests\Unit\NamespaceFirst;

use LFreeze\PackagePrivate\PackagePrivate;
use LFreeze\PackagePrivate\AssignAttribute;

class Callee {
    use AssignAttribute;

    #[PackagePrivate]
    private int $packagePrivateInt = 11111;

    #[PackagePrivate]
    private string $packagePrivateString = "package-private-string";

    private int $privateInt = 99999;
    private string $privateString = "private-string";

    public function __construct(public $x = 'DefaultPropertyX', public $y = 'DefaultPropertyY') {

    }

    #[PackagePrivate]
    private function packagePrivateMethod(?string $param = null, ?string $param2 = null) {
        return __FUNCTION__ . ($param ?? '') . ($param2 ?? '');
    }

    #[PackagePrivate]
    private function packagePrivateMethodWithParam(?string $param = null, ?string $param2 = null) {
        return __FUNCTION__ ;
    }

    private function privateMethod() {
        return __FUNCTION__ ;
    }

    public function publicMethod(?string $param = null) {
        return __FUNCTION__ ;
    }
}
