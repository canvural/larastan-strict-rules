<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use Closure;
use Illuminate\Console\Command;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\File\FileHelper;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpFunctionFromParserNodeReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VoidType;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use function count;
use function stripos;

/** @implements Rule<InClassMethodNode> */
class ListenerShouldHaveVoidReturnTypeRule implements Rule
{
    /** @param string[] $listenerPaths */
    public function __construct(private FileHelper $fileHelper, private array $listenerPaths)
    {
    }

    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /** @return RuleError[] */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            return [];
        }

        $originalNode = $node->getOriginalNode();

        // We only care for methods that have handle as the name, and have some statements in the body
        if ($originalNode->stmts === null || $originalNode->name->name !== 'handle') {
            return [];
        }

        $classReflection  = $scope->getClassReflection();
        $methodReflection = $scope->getFunction();

        if ($methodReflection === null) {
            return [];
        }

        if ($this->isExcluded($classReflection, $methodReflection)) {
            return [];
        }

        // handle method should except event as parameter
        if (count(ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getParameters()) < 1) {
            return [];
        }

        $fileName = $classReflection->getFileName();

        if ($fileName === null) {
            return [];
        }

        foreach ($this->listenerPaths as $listenerPath) {
            $absolutePath = $this->fileHelper->normalizePath($this->fileHelper->absolutizePath($listenerPath));

            if (stripos($fileName, $absolutePath) !== false) {
                break;
            }

            return [];
        }

        if (! (new VoidType())->isSuperTypeOf(ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType())->yes()) {
            return [
                RuleErrorBuilder::message("Listeners handle method should have 'void' return type.")
                    ->identifier('larastanStrictRules.listenerShouldHaveVoidReturnType')
                    ->build(),
            ];
        }

        return [];
    }

    protected function isExcluded(ClassReflection $classReflection, PhpFunctionFromParserNodeReflection $methodReflection): bool
    {
        if ($this->isCommand($classReflection)) {
            return true;
        }

        if ($this->isMiddleware($classReflection, $methodReflection)) {
            return true;
        }

        return false;
    }

    protected function isCommand(ClassReflection $classReflection): bool
    {
        return $classReflection->is(Command::class);
    }

    /**
     * Laravel middlewares do not implement any contract, so they can only be
     * recognized by the signature of their handle method: they accept the
     * request as the first parameter and the "next" closure as the second one.
     */
    protected function isMiddleware(ClassReflection $classReflection, PhpFunctionFromParserNodeReflection $methodReflection): bool
    {
        if ($classReflection->isInterface() || $classReflection->isEnum()) {
            return false;
        }

        $parameters = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getParameters();

        if (count($parameters) < 2) {
            return false;
        }

        if (! $this->isNextClosure($parameters[1])) {
            return false;
        }

        return $this->isRequest($parameters[0]);
    }

    protected function isRequest(ParameterReflection $parameter): bool
    {
        $type = $parameter->getType();

        if ($type instanceof MixedType) {
            return $parameter->getName() === 'request';
        }

        return (new ObjectType(SymfonyRequest::class))->isSuperTypeOf($type)->yes();
    }

    protected function isNextClosure(ParameterReflection $parameter): bool
    {
        $type = $parameter->getType();

        if ($type instanceof MixedType) {
            return $parameter->getName() === 'next';
        }

        return (new ObjectType(Closure::class))->isSuperTypeOf($type)->yes();
    }
}
