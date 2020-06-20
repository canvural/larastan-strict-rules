<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<NoValidationInControllerRule>
 */
class NoValidationInControllerRuleTest extends RuleTestCase
{
    /**
     * @return Rule<MethodCall>
     */
    protected function getRule(): Rule
    {
        return new NoValidationInControllerRule();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/validation.php'], [
            [
                'Request validation should be done in FormRequest not in Controller.',
                14,
            ],
            [
                'Request validation should be done in FormRequest not in Controller.',
                15,
            ],
            [
                'Request validation should be done in FormRequest not in Controller.',
                20,
            ],
            [
                'Request validation should be done in FormRequest not in Controller.',
                21,
            ],
            [
                'Request validation should be done in FormRequest not in Controller.',
                26,
            ],
            [
                'Request validation should be done in FormRequest not in Controller.',
                27,
            ],
        ]);
    }
}
