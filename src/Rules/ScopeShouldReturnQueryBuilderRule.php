<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpParameterFromParserNodeReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;

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
            ! $classReflection->isSubclassOf(Model::class) &&
            ! $classReflection->isSubclassOf(Builder::class)
        ) {
            return [];
        }

        if (count($originalNode->params) === 0) {
            return [];
        }

        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());

        /** @var PhpParameterFromParserNodeReflection $firstParameter */
        $firstParameter = $parametersAcceptor->getParameters()[0];

        if (! ($firstParameter->getType() instanceof ObjectType)) {
            return [];
        }

        if ($firstParameter->getType()->getClassName() !== Builder::class && ! $this->provider->getClass($firstParameter->getType()->getClassName())->isSubclassOf(Builder::class)) {
            return [];
        }

        $returnType = $parametersAcceptor->getReturnType();

        if (! ($returnType instanceof ObjectType)) {
            return [
                RuleErrorBuilder::message('Query scope should return query builder instance.')
                    ->identifier('larastanStrictRules.scopeShouldReturnQueryBuilderRule')
                    ->build(),
            ];
        }

        if ($returnType->getClassName() !== Builder::class && ! $this->provider->getClass($returnType->getClassName())->isSubclassOf(Builder::class)) {
            return [
                RuleErrorBuilder::message('Query scope should return query builder instance.')
                    ->identifier('larastanStrictRules.scopeShouldReturnQueryBuilderRule')
                    ->build(),
            ];
        }

        return [];
    }
}
