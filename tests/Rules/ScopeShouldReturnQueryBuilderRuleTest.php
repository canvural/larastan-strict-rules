<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ScopeShouldReturnQueryBuilderRule>
 */
class ScopeShouldReturnQueryBuilderRuleTest extends RuleTestCase
{
    /**
     * @return Rule<InClassMethodNode>
     */
    protected function getRule(): Rule
    {
        return new ScopeShouldReturnQueryBuilderRule($this->createReflectionProvider());
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
        $this->analyse([__DIR__ . '/data/scopes.php'], [
            [
                'Query scope should return query builder instance.',
                13,
            ],
            [
                'Query scope should return query builder instance.',
                18,
            ],
            [
                'Query scope should return query builder instance.',
                27,
            ],
            [
                'Query scope should return query builder instance.',
                36,
            ],
        ]);
    }
}
