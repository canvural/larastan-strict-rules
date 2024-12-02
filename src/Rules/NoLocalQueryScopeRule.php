<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\Php\PhpParameterFromParserNodeReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

use function count;
use function strpos;

/** @implements Rule<InClassMethodNode> */
final class NoLocalQueryScopeRule implements Rule
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
     *
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $originalNode = $node->getOriginalNode();
        $methodName   = $originalNode->name->toString();

        if ($originalNode->stmts === null || strpos($methodName, 'scope') !== 0 || ! $scope->isInClass()) {
            return [];
        }

        $classReflection  = $scope->getClassReflection();
        $methodReflection = $scope->getFunction();

        if ($methodReflection === null) {
            return [];
        }

        if (! $classReflection->isSubclassOf(Model::class)) {
            return [];
        }

        if (count($originalNode->params) === 0) {
            return [];
        }

        /** @var PhpParameterFromParserNodeReflection $firstParameter */
        $firstParameter = $methodReflection->getParameters()[0];

        $parameterClassNames = $firstParameter->getType()->getObjectClassNames();

        if (count($parameterClassNames) !== 1) {
            return [];
        }

        if ($parameterClassNames[0] !== Builder::class && ! $this->provider->getClass($parameterClassNames[0])->isSubclassOf(Builder::class)) {
            return [];
        }

        return [RuleErrorBuilder::message('Local query scopes should not be used.')->build()];
    }
}
