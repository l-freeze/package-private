[![Test](https://github.com/l-freeze/package-private/actions/workflows/ci.yaml/badge.svg)](https://github.com/l-freeze/package-private/actions/workflows/ci.yaml)

コンストラクターび名前付き引数を生贄にパッケージプライベートを召喚


# Supported

- php 8.2 later

# Unsupported

- Constructor call with named arguments
- System provided error message (cannot access property, Call to private method, ...)

# Example

[example](https://github.com/l-freeze/package-private/tree/master/example)

```php
//callee.php
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

    #[PackagePrivate]
    private function packagePrivateMethod(?string $param = null, ?string $param2 = null) {
        return __FUNCTION__ . ($param ?? '') . ($param2 ?? '');
    }

    public function __construct(public $namedArguments1 = 'DefaultPropertyX', public $namedArguments2 = 'DefaultPropertyY') {
    }

}

```

```php
//caller.php
<?php
declare(strict_types=1);
namespace Example\NamespaceA;

readonly final class CallerInsideSpaceA {

    public function __construct(private ?string $param1 = null,private  ?int $param2 = null) {
    }

    public function do() {
        Callee::assignCallerNamespaceName(__NAMESPACE__);

        $example = match (is_null($this->param1) && is_null($this->param2)) {
            true => Callee::create(),
            false => Callee::create($this->param1, $this->param2)
        };

        $example->packagePrivateInt = 13;
        echo '$example->packagePrivateInt:'. $example->packagePrivateInt.PHP_EOL;
        echo $example->packagePrivateMethod(param:" AssignParam1 ", param2: " AssignParam2 ").PHP_EOL;
        echo $example->packagePrivateMethod(param2: " AssignParam2 ", param:" AssignParam1 ").PHP_EOL;
    }
}
```
