<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Larastan\Larastan\Methods\BuilderHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

use function sprintf;
use function str_starts_with;
use function ucfirst;

/** @implements Rule<MethodCall> */
final class NoDynamicWhereRule implements Rule
{
    /** @var ReflectionProvider */
    private $provider;

    /** @var BuilderHelper  */
    private $builderHelper;

    public function __construct(ReflectionProvider $provider, BuilderHelper $builderHelper)
    {
        $this->provider      = $provider;
        $this->builderHelper = $builderHelper;
    }

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

        $methodName = $node->name->toString();

        if ($methodName === 'where' || ! str_starts_with($methodName, 'where')) {
            return [];
        }

        $calledOnType = $this->getCalledOnType($node, $scope);

        if (! $calledOnType instanceof ObjectType) {
            return [];
        }

        if (
            $calledOnType->isInstanceOf(Model::class)->no() &&
            $calledOnType->isInstanceOf(EloquentBuilder::class)->no() &&
            $calledOnType->isInstanceOf(QueryBuilder::class)->no()
        ) {
            return [];
        }

        /** @var ClassReflection|null $calledOnReflection */
        $calledOnReflection = $calledOnType->getClassReflection();

        if ($calledOnReflection === null) {
            return [];
        }

        if ($calledOnReflection->hasNativeMethod($methodName)) {
            return [];
        }

        $model = $this->findModel($calledOnReflection);

        if ($model !== null) {
            $eloquentBuilder = $this->builderHelper->determineBuilderName($model);
        } else {
            $eloquentBuilder = EloquentBuilder::class;
        }

        if (
            $this->provider->getClass(Model::class)->hasNativeMethod($methodName) ||
            $model && $this->provider->getClass($model)->hasNativeMethod('scope' . ucfirst($methodName)) ||
            $this->provider->getClass($eloquentBuilder)->hasNativeMethod($methodName) ||
            $this->provider->getClass(QueryBuilder::class)->hasNativeMethod($methodName) ||
            $this->provider->getClass(BelongsToMany::class)->hasNativeMethod($methodName)
        ) {
            return [];
        }

        if ($calledOnType instanceof GenericObjectType) {
            foreach ($calledOnType->getTypes() as $type) {
                if ($type instanceof ObjectType && $type->getClassReflection() !== null && $type->getClassReflection()->hasNativeMethod($methodName)) {
                    return [];
                }
            }
        }

        return [
            RuleErrorBuilder::message(sprintf(
                "Dynamic where method '%s' should not be used.",
                $methodName,
            ))
                ->identifier('larastanStrictRules.noDynamicWhere')
                ->build(),
        ];
    }

    private function getCalledOnType(MethodCall $node, Scope $scope): Type
    {
        $methodCall = $node;

        while ($methodCall->var instanceof MethodCall) {
            $methodCall = $methodCall->var;
        }

        $calledOnType = $scope->getType($methodCall->var);

        if ($calledOnType instanceof ThisType) {
            $calledOnType = $calledOnType->getStaticObjectType();
        }

        return TypeCombinator::removeNull($calledOnType);
    }

    private function findModel(ClassReflection $calledOnReflection): string|null
    {
        if ($calledOnReflection->isSubclassOf(Model::class)) {
            return $calledOnReflection->getName();
        }

        if (
            $calledOnReflection->getName() === EloquentBuilder::class ||
            $calledOnReflection->isSubclassOf(EloquentBuilder::class)
        ) {
            $modelType = $calledOnReflection->getActiveTemplateTypeMap()->getType('TModelClass');

            if (! $modelType instanceof ObjectType) {
                return null;
            }

            return $modelType->getClassName();
        }

        return null;
    }
}
