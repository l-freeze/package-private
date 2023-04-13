<?php
declare(strict_types=1);
namespace Example\NamespaceB;

use Example\NamespaceA\Callee;
class PackagePrivatePropertyOverrideNg {

    public function __construct(private ?string $param1 = null,private  ?int $param2 = null) {

    }
    public function do() {
        echo "[ ".__NAMESPACE__." ]".PHP_EOL;
        Callee::assignCallerNamespaceName(__NAMESPACE__);
        $example = match (is_null($this->param1) && is_null($this->param2)) {
            true => Callee::create(),
            false => Callee::create($this->param1, $this->param2)
        };
        echo $example->x.PHP_EOL;
        echo $example->y.PHP_EOL;
        $example->packagePrivateTestInt = 13;
        echo '$example->packagePrivateTestInt:'. $example->packagePrivateTestInt.PHP_EOL;
        $example->packagePrivateTestString = "update packagePrivateTestString";
        echo '$example->packagePrivateTestString:'.$example->packagePrivateTestString.PHP_EOL;
        $example->privateInt= 9999;
        echo $example->privateInt.PHP_EOL;
    }
}
