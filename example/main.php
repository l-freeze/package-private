<?php
declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Example\NamespaceA\CallerInsideSpaceA;
use Example\NamespaceB\CallerInsideSpaceB;

echo ">>>>OK-A".PHP_EOL;
try{
    $propertyOverride = new CallerInsideSpaceA();
    $propertyOverride->do();    
} catch (Throwable $e) {
    echo "{$e->getFile()}:{$e->getLine()}".PHP_EOL;
    echo $e->getMessage().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
}
echo PHP_EOL;

echo ">>>>OK-B".PHP_EOL;
try{
    $propertyOverride = new CallerInsideSpaceA("a", 1);
    $propertyOverride->do();  
} catch (Throwable $e) {
    echo "{$e->getFile()}:{$e->getLine()}".PHP_EOL;
    echo $e->getMessage().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
}
echo PHP_EOL;


echo ">>>>NG".PHP_EOL;
try{
    $propertyOverride = new CallerInsideSpaceB();
    $propertyOverride->do();
} catch (Throwable $e) {
    echo "{$e->getFile()}:{$e->getLine()}".PHP_EOL;
    echo $e->getMessage().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
}
echo PHP_EOL;
