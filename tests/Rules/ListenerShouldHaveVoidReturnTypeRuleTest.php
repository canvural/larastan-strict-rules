<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/** @extends RuleTestCase<ListenerShouldHaveVoidReturnTypeRule> */
class ListenerShouldHaveVoidReturnTypeRuleTest extends RuleTestCase
{
    /**
     * Paths to analyse as listener paths. When null the paths configured in
     * the test container are used.
     *
     * @var string[]|null
     */
    private array|null $listenerPaths = null;

    protected function getRule(): Rule
    {
        return new ListenerShouldHaveVoidReturnTypeRule(
            new FileHelper(__DIR__ . '/data'),
            $this->listenerPaths ?? $this->getContainer()->getParameter('listenerPaths'),
        );
    }

    /** @return string[] */
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

    public function testDoesNotReportMiddlewares(): void
    {
        // No listener paths configured, so every class with a handle method is analysed.
        $this->listenerPaths = [];

        $this->analyse([__DIR__ . '/data/Middlewares/FooMiddleware.php'], []);
    }

    public function testDoesNotReportCommands(): void
    {
        // No listener paths configured, so every class with a handle method is analysed.
        $this->listenerPaths = [];

        $this->analyse([__DIR__ . '/data/Commands/FooCommand.php'], []);
    }
}
