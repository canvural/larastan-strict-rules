<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

use function count;
use function str_starts_with;

/** @implements Rule<InClassMethodNode> */
final class ScopeShouldReturnQueryBuilderRule implements Rule
{
    public function __construct(private ReflectionProvider $provider)
    {
    }

    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /**
     * @param InClassMethodNode $node
     *
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            return [];
        }

        $originalNode = $node->getOriginalNode();

        if ($originalNode->stmts === null || ! str_starts_with($originalNode->name->name, 'scope')) {
            return [];
        }

        $classReflection  = $scope->getClassReflection();
        $methodReflection = $scope->getFunction();

        if ($methodReflection === null) {
            return [];
        }

        if (
            ! $classReflection->is(Model::class) &&
            ! $classReflection->is(Builder::class)
        ) {
            return [];
        }

        if (count($originalNode->params) === 0) {
            return [];
        }

        $firstParameter = $methodReflection->getParameters()[0];

        $parameterClassNames = $firstParameter->getType()->getObjectClassNames();

        if (count($parameterClassNames) < 1) {
            return [];
        }

        if (! $this->provider->getClass($parameterClassNames[0])->is(Builder::class)) {
            return [];
        }

        $returnTypeClassNames = $methodReflection->getReturnType()->getObjectClassNames();

        if (count($returnTypeClassNames) !== 1) {
            return [
                RuleErrorBuilder::message('Query scope should return query builder instance.')
                    ->identifier('larastanStrictRules.scopeShouldReturnQueryBuilderRule')
                    ->build(),
            ];
        }

        if (! $this->provider->getClass($returnTypeClassNames[0])->is(Builder::class)) {
            return [
                RuleErrorBuilder::message('Query scope should return query builder instance.')
                    ->identifier('larastanStrictRules.scopeShouldReturnQueryBuilderRule')
                    ->build(),
            ];
        }

        return [];
    }
}
