<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/** @extends RuleTestCase<NoLocalQueryScopeRule> */
class NoLocalQueryScopeRuleTest extends RuleTestCase
{
    /** @return Rule<InClassMethodNode> */
    protected function getRule(): Rule
    {
        return new NoLocalQueryScopeRule($this->createReflectionProvider());
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/query-scopes.php'], [
            [
                'Local query scopes should not be used.',
                15,
            ],
            [
                'Local query scopes should not be used.',
                20,
            ],
        ]);
    }
}
