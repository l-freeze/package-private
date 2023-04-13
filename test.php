<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Example\NamespaceA\PackagePrivatePropertyOverrideOk;
use Example\NamespaceD\PackagePrivatePropertyOverrideNg;

echo ">>>>OK-A".PHP_EOL;
try{
    $propertyOverride = new PackagePrivatePropertyOverrideOk();
    $propertyOverride->do();    
} catch (Throwable $e) {
    echo "{$e->getFile()}:{$e->getLine()}".PHP_EOL;
    echo $e->getMessage().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
}
echo PHP_EOL;

echo ">>>>OK-B".PHP_EOL;
try{
    $propertyOverride = new PackagePrivatePropertyOverrideOk("a", 1);
    $propertyOverride->do();    
} catch (Throwable $e) {
    echo "{$e->getFile()}:{$e->getLine()}".PHP_EOL;
    echo $e->getMessage().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
}
echo PHP_EOL;


echo ">>>>NG".PHP_EOL;
try{
    $propertyOverride = new PackagePrivatePropertyOverrideNg();
    $propertyOverride->do();
} catch (Throwable $e) {
    echo "{$e->getFile()}:{$e->getLine()}".PHP_EOL;
    echo $e->getMessage().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
}
echo PHP_EOL;
