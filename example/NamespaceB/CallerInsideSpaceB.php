<?php
declare(strict_types=1);
namespace Example\NamespaceB;

use Example\NamespaceA\Callee;

readonly final class CallerInsideSpaceB {

    public function __construct(private ?string $param1 = null,private  ?int $param2 = null) {
    }

    public function do() {
        echo PHP_EOL."[[[ ".__NAMESPACE__." ]]]".PHP_EOL;
        Callee::assignCallerNamespaceName(__NAMESPACE__);

        //$example = (new Callee(namedArguments1: "name1", namedArguments2: "name2"))->create();
        $example = match (is_null($this->param1) && is_null($this->param2)) {
            true => (new Callee(namedArguments2: "partialarguments"))->__create(),
            false => (new Callee(namedArguments1: $this->param1, namedArguments2: $this->param2))->__create()
        };

        //get property
        //echo $example->x.PHP_EOL;
        //echo $example->y.PHP_EOL;
        //echo $example->namedArguments1.PHP_EOL;
        echo $example->namedArguments2.PHP_EOL;
        $example->packagePrivateInt = 13;
        echo '$example->packagePrivateInt:'. $example->packagePrivateInt.PHP_EOL;
        $example->packagePrivateString = "update packagePrivateString";
        echo '$example->packagePrivateString:'.$example->packagePrivateString.PHP_EOL;

        /**
         * Cannot access property example
         */
        //$example->packagePrivateInt = 13;
        //echo '$example->packagePrivateInt:'. $example->packagePrivateInt.PHP_EOL;
        //$example->packagePrivateTestString = "update packagePrivateTestString";
        //echo '$example->packagePrivateString:'.$example->packagePrivateString.PHP_EOL;
        
        echo $example->packagePrivateMethod().PHP_EOL;
        echo $example->publicMethod().PHP_EOL;
        echo $example->packagePrivateMethod(": CALL", "---a").PHP_EOL;
        echo $example->packagePrivateMethod(param:" AssignParam1 ", param2: " AssignParam2 ").PHP_EOL;
        echo $example->packagePrivateMethod(param2: " AssignParam2 ", param:" AssignParam1 ").PHP_EOL;
        echo $example->publicMethod(": CALL").PHP_EOL;

        /**
         * Cannot call to private method example
         */
        //echo $example->privateMethod().PHP_EOL;
        //echo $example->privateMethod(": CALL").PHP_EOL;

        echo $example->main();

    }
}
