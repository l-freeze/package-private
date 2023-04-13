[![Test](https://github.com/l-freeze/package-private/actions/workflows/ci.yaml/badge.svg)](https://github.com/l-freeze/package-private/actions/workflows/ci.yaml)

名前付き引数を生贄にパッケージプライベートを召喚


# Supported

- php 8.2 later

# Unsupported

- Method call with named arguments
- System provided error message (cannot access...)

# Usage example

```php
<?php
//callee
declare(strict_types=1);
namespace Example\NamespaceA;

use LFreeze\PackagePrivate\PackagePrivate;
use LFreeze\PackagePrivate\AssignAttribute;

final class Callee {
    use AssignAttribute;

    #[PackagePrivate]
    private int $packagePrivateTestInt = 213;

    #[PackagePrivate]
    private function packagePrivateMethod(?string $param = null, ?string $param2 = null) {
        return __FUNCTION__ . ($param ?? '') . ($param2 ?? '');
    }
}
```

```php
//caller
<?php
declare(strict_types=1);
namespace Example\NamespaceA;

readonly final class PackagePrivatePropertyOverrideOk {

    public function __construct(private ?string $param1 = null,private  ?int $param2 = null) {
    }

    public function do() {
        Callee::assignCallerNamespaceName(__NAMESPACE__);
        $example = match (is_null($this->param1) && is_null($this->param2)) {
            true => Callee::create(),
            false => Callee::create($this->param1, $this->param2)
        };

        $example->packagePrivateTestInt = 13;
        echo '$example->packagePrivateTestInt:'. $example->packagePrivateTestInt.PHP_EOL;
        $example->packagePrivateMethod();
    }
}
```

# example
```bash
php test.php
```