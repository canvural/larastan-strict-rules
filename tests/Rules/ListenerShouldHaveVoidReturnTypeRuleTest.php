<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ListenerShouldHaveVoidReturnTypeRule>
 */
class ListenerShouldHaveVoidReturnTypeRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ListenerShouldHaveVoidReturnTypeRule(
            new FileHelper(__DIR__ . '/data'),
            $this->getContainer()->getParameter('listenerPaths'),
        );
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/data/listeners.neon'];
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/Listeners/FooBarListener.php'], [
            [
                "Listeners handle method should have 'void' return type.",
                7,
            ],
            [
                "Listeners handle method should have 'void' return type.",
                23,
            ],
        ]);
    }
}
