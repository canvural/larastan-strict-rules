<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpFunctionReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function sprintf;
use function stripos;

/** @implements Rule<FuncCall> */
final class NoGlobalLaravelFunctionRule implements Rule
{
    /** @var ReflectionProvider  */
    private $provider;

    /** @var string[] */
    private $allowedFunctions;

    /** @param string[] $allowedFunctions */
    public function __construct(ReflectionProvider $provider, array $allowedFunctions)
    {
        $this->provider         = $provider;
        $this->allowedFunctions = $allowedFunctions;
    }

    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }

    /**
     * @param Node\Expr\FuncCall $node
     *
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Node\Name) {
            return [];
        }

        $functionReflection = $this->provider->getFunction($node->name, $scope);

        if (! $functionReflection instanceof PhpFunctionReflection) {
            return [];
        }

        $fileName = $functionReflection->getFileName();

        if ($fileName === null) {
            return [];
        }

        if (
            stripos($fileName, 'illuminate/support/helpers.php') !== false ||
            stripos($fileName, 'illuminate/collections/helpers.php') !== false ||
            stripos($fileName, 'illuminate/foundation/helpers.php') !== false
        ) {
            if (in_array($functionReflection->getName(), $this->allowedFunctions, true)) {
                return [];
            }

            return [
                RuleErrorBuilder::message(sprintf(
                    "Global helper function '%s' should not be used.",
                    $node->name,
                ))
                    ->identifier('larastanStrictRules.noGlobalLaravelFunction')
                    ->build(),
            ];
        }

        return [];
    }
}
