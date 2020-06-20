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
use function strpos;

/**
 * @implements Rule<InClassMethodNode>
 */
final class ScopeShouldReturnQueryBuilderRule implements Rule
{
    /** @var ReflectionProvider */
    private $provider;

    public function __construct(ReflectionProvider $provider)
    {
        $this->provider = $provider;
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

        if ($originalNode->stmts === null || strpos($originalNode->name->name, 'scope') !== 0) {
            return [];
        }

        $classReflection  = $scope->getClassReflection();
        $methodReflection = $scope->getFunction();

        if ($classReflection === null || $methodReflection === null) {
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
            return [RuleErrorBuilder::message('Query scope should return query builder instance.')->build()];
        }

        if (! $this->provider->getClass($returnType->getClassName())->isSubclassOf(Builder::class)) {
            return [RuleErrorBuilder::message('Query scope should return query builder instance.')->build()];
        }

        return [];
    }
}
