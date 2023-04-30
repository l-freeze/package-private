<?php
declare(strict_types=1);

namespace LFreeze\PackagePrivate;

use LFreeze\PackagePrivate\Exception\CallerNamespaceUndefinedException;
use LFreeze\PackagePrivate\Exception\UninitializedException;
use ReflectionClass;
use ReflectionProperty;
use BadMethodCallException;
use ReflectionParameter;
use UnexpectedValueException;

trait DummyTrait {
}