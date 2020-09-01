<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoGlobalLaravelFunctionRule>
 */
class NoGlobalLaravelFunctionRuleTest extends RuleTestCase
{
    /**
     * @return Rule<FuncCall>
     */
    protected function getRule(): Rule
    {
        return new NoGlobalLaravelFunctionRule($this->createReflectionProvider(), ['app']);
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
        $this->analyse([__DIR__ . '/data/helper-functions.php'], [
            [
                "Global helper function 'value' should not be used.",
                12,
            ],
            [
                "Global helper function 'base_path' should not be used.",
                17,
            ],
        ]);
    }
}
