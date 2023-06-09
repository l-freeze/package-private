<?php
declare(strict_types=1);
namespace LFreeze\PackagePrivate;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_PROPERTY|Attribute::TARGET_METHOD|Attribute::TARGET_CLASS)]
readonly final class PackagePrivate
{

    /**
     * @param array<string> $allowedNameSpace
     */
    public function __construct(readonly private ?array $allowedNameSpace) {}
}