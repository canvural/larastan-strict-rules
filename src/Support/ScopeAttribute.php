<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Support;

use Illuminate\Database\Eloquent\Attributes\Scope;
use PHPStan\Reflection\ExtendedMethodReflection;

/** @internal */
final class ScopeAttribute
{
    /**
     * Whether the method is a local query scope declared with the Scope attribute.
     * Like Laravel, private methods are not considered to be scopes.
     */
    public static function isOn(ExtendedMethodReflection $method): bool
    {
        if ($method->isPrivate()) {
            return false;
        }

        foreach ($method->getAttributes() as $attribute) {
            if ($attribute->getName() === Scope::class) {
                return true;
            }
        }

        return false;
    }
}
