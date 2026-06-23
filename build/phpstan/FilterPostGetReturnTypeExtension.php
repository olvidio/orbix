<?php

declare(strict_types=1);

namespace Orbix\PHPStan;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\Php\FilterFunctionReturnTypeHelper;
use PHPStan\Type\Type;

/**
 * Da a `filter_post()` / `filter_get()` (src/shared/domain/helpers/func_input.php)
 * la misma inferencia de tipo de retorno que
 * `filter_input(INPUT_POST, ...)` / `filter_input(INPUT_GET, ...)`.
 *
 * Sin esto, los wrappers declaran `mixed` y se perderían cientos de tipos
 * (p. ej. `(int)filter_post('x', FILTER_VALIDATE_INT)` daría `cast.int`).
 * Reaprovecha el helper interno de PHPStan que ya tipa `filter_input`.
 */
final class FilterPostGetReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    public function __construct(private readonly FilterFunctionReturnTypeHelper $helper)
    {
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['filter_post', 'filter_get'], true);
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        Scope $scope
    ): ?Type {
        $args = $functionCall->getArgs();
        if ($args === []) {
            return null;
        }

        $varNameType = $scope->getType($args[0]->value);
        $filterType = isset($args[1]) ? $scope->getType($args[1]->value) : null;
        $flagsType = isset($args[2]) ? $scope->getType($args[2]->value) : null;

        $inputConst = $functionReflection->getName() === 'filter_post' ? INPUT_POST : INPUT_GET;

        return $this->helper->getInputType(
            new ConstantIntegerType($inputConst),
            $varNameType,
            $filterType,
            $flagsType,
        );
    }
}
