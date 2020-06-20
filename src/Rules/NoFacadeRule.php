<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Illuminate\Support\Facades\Facade;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

use function sprintf;
use function strpos;

/**
 * @implements Rule<StaticCall>
 */
final class NoFacadeRule implements Rule
{
    /** @var ReflectionProvider  */
    private $provider;

    public function __construct(ReflectionProvider $provider)
    {
        $this->provider = $provider;
    }

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    /**
     * @param StaticCall $node
     *
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Node\Name) {
            return [];
        }

        $className = $scope->resolveName($node->class);

        try {
            $class = $this->provider->getClass($className);

            if ($class->isSubclassOf(Facade::class)) {
                return [
                    RuleErrorBuilder::message(sprintf(
                        '%s facade should not be used.',
                        $className
                    ))->build(),
                ];
            }
        } catch (ClassNotFoundException $e) {
            if (strpos($className, 'Facades\\') === 0) {
                return [
                    RuleErrorBuilder::message(sprintf(
                        '%s facade should not be used.',
                        $className
                    ))->build(),
                ];
            }
        }

        return [];
    }
}
