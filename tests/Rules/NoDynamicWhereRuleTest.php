<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use NunoMaduro\Larastan\Methods\BuilderHelper;
use NunoMaduro\Larastan\Methods\MacroMethodsClassReflectionExtension;
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
        return new NoDynamicWhereRule($this->createReflectionProvider(), new BuilderHelper(
            $this->createReflectionProvider(),
            false,
            $this->getContainer()->getByType(MacroMethodsClassReflectionExtension::class),
        ));
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
                21,
            ],
            [
                "Dynamic where method 'whereBar' should not be used.",
                32,
            ],
            [
                "Dynamic where method 'whereBar' should not be used.",
                34,
            ],
            [
                "Dynamic where method 'whereBaz' should not be used.",
                51,
            ],
        ]);
    }
}
