<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use NunoMaduro\Larastan\Methods\BuilderHelper;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoDynamicWhereRule>
 */
class NoDynamicWhereRuleTest extends RuleTestCase
{
    /**
     * @return Rule<MethodCall>
     */
    protected function getRule(): Rule
    {
        return new NoDynamicWhereRule($this->createReflectionProvider(), new BuilderHelper($this->createBroker()));
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/extension.neon'];
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/dynamic-where.php'], [
            [
                "Dynamic where method 'whereBar' should not be used.",
                20,
            ],
            [
                "Dynamic where method 'whereBar' should not be used.",
                31,
            ],
            [
                "Dynamic where method 'whereBar' should not be used.",
                33,
            ],
            [
                "Dynamic where method 'whereBaz' should not be used.",
                50,
            ],
        ]);
    }
}
