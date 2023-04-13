<?php
declare(strict_types=1);
namespace LFreeze\PackagePrivate;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_PROPERTY|Attribute::TARGET_METHOD)]
readonly final class PackagePrivate
{
    public function __construct() {}
}