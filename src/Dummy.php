<?php
declare(strict_types=1);
namespace LFreeze\PackagePrivate;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_PROPERTY|Attribute::TARGET_METHOD|Attribute::TARGET_CLASS)]
readonly final class Dummy
{
    /**
     * @param array<string> $nameSpace
     */
    public function __construct(private ?array $namespaces) {}
}