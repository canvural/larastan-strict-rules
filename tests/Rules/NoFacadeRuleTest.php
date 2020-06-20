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
                11,
            ],
            [
                'Event facade should not be used.',
                13,
            ],
            [
                'Facades\App\User facade should not be used.',
                15,
            ],
        ]);
    }
}
