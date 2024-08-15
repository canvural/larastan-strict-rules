<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use PHPStan\Type\UnionType;

use function in_array;

/** @implements Rule<MethodCall> */
final class NoValidationInControllerRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     *
     * @return RuleError[]
     *
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Node\Identifier) {
            return [];
        }

        $methodName = $node->name->name;

        if (! in_array($methodName, ['validate', 'validateWithBag'], true) || ! $scope->isInClass()) {
            return [];
        }

        $classReflection = $scope->getClassReflection();

        if (! $classReflection->isSubclassOf(Controller::class)) {
            return [];
        }

        $calledOn = $scope->getType($node->var);

        if ($calledOn instanceof ObjectType) {
            if ($calledOn->isInstanceOf(Request::class)->yes()) {
                return [
                    RuleErrorBuilder::message('Request validation should be done in FormRequest not in Controller.')
                        ->identifier('larastanStrictRules.noValidationInController')
                        ->build(),
                ];
            }
        } elseif (($calledOn instanceof ThisType) && $calledOn->getStaticObjectType()->isInstanceOf(Controller::class)->yes()) {
            return [
                RuleErrorBuilder::message('Request validation should be done in FormRequest not in Controller.')
                    ->identifier('larastanStrictRules.noValidationInController')
                    ->build(),
            ];
        } elseif ($calledOn instanceof UnionType && $calledOn->accepts(new ObjectType(Request::class), true)->yes()) {
            return [
                RuleErrorBuilder::message('Request validation should be done in FormRequest not in Controller.')
                    ->identifier('larastanStrictRules.noValidationInController')
                    ->build(),
            ];
        }

        return [];
    }
}
