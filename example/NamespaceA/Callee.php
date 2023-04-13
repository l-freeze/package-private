<?php
declare(strict_types=1);
namespace Example\NamespaceA;

use LFreeze\PackagePrivate\PackagePrivate;
use LFreeze\PackagePrivate\AssignAttribute;

final class Callee {
    use AssignAttribute;

    #[PackagePrivate]
    private int $packagePrivateTestInt = 213;

    #[PackagePrivate]
    private string $packagePrivateTestString = "stringstring";

    private int $privateInt = 123;

    public function __construct(public $x = 'DefaultPropertyX', public $y = 'DefaultPropertyY') {

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

}