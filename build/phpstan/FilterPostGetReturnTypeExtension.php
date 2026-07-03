<?php

declare(strict_types=1);

namespace Orbix\PHPStan;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Php\FilterFunctionReturnTypeHelper;
use PHPStan\Type\Type;
use src\shared\domain\helpers\FilterPostGet;

/**
 * Da a {@see \src\shared\domain\helpers\FilterPostGet::post()} / {@see \src\shared\domain\helpers\FilterPostGet::get()}
 * la misma inferencia de tipo de retorno que
 * `filter_input(INPUT_POST, ...)` / `filter_input(INPUT_GET, ...)`.
 */
final class FilterPostGetReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function __construct(private readonly FilterFunctionReturnTypeHelper $helper)
    {
    }

    public function getClass(): string
    {
        return \src\shared\domain\helpers\FilterPostGet::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return in_array($methodReflection->getName(), ['post', 'get'], true);
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): ?Type {
        $args = $methodCall->getArgs();
        if ($args === []) {
            return null;
        }

        $varNameType = $scope->getType($args[0]->value);
        $filterType = isset($args[1]) ? $scope->getType($args[1]->value) : null;
        $flagsType = isset($args[2]) ? $scope->getType($args[2]->value) : null;

        $inputConst = $methodReflection->getName() === 'post' ? INPUT_POST : INPUT_GET;

        return $this->helper->getInputType(
            new ConstantIntegerType($inputConst),
            $varNameType,
            $filterType,
            $flagsType,
        );
    }
}
