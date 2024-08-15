<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

use function count;
use function preg_match;

/** @implements Rule<InClassMethodNode> */
final class NoPropertyAccessorRule implements Rule
{
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

        if ($originalNode->stmts === null || ! preg_match('/(?<=^|;)get([^;]+?)Attribute(;|$)/', $methodName) || ! $scope->isInClass()) {
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

        return [
            RuleErrorBuilder::message('Model property accessors should not be used.')
                ->identifier('larastanStrictRules.noPropertyAccessor')
                ->build(),
        ];
    }
}
