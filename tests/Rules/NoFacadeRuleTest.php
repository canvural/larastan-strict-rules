<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoFacadeRule>
 */
class NoFacadeRuleTest extends RuleTestCase
{
    /**
     * @return Rule<StaticCall>
     */
    protected function getRule(): Rule
    {
        return new NoFacadeRule($this->createReflectionProvider());
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/facades.php'], [
            [
                'Illuminate\Support\Facades\Queue facade should not be used.',
                10,
            ],
            [
                'RateLimiter facade should not be used.',
                12,
            ],
        ]);
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/facadeAlias.neon'];
    }
}
