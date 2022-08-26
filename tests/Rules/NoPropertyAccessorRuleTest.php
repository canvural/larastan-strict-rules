<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/** @extends RuleTestCase<NoPropertyAccessorRule> */
class NoPropertyAccessorRuleTest extends RuleTestCase
{
    /** @return Rule<InClassMethodNode> */
    protected function getRule(): Rule
    {
        return new NoPropertyAccessorRule();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/property-accessor.php'], [
            [
                'Model property accessors should not be used.',
                27,
            ],
        ]);
    }
}
