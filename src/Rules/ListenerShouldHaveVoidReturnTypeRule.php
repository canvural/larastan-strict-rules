<?php

declare(strict_types=1);

namespace Vural\LarastanStrictRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\File\FileHelper;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\VoidType;

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
}
