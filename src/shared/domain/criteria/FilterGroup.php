<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

use InvalidArgumentException;

/**
 * Grupo de filtros unidos por AND o por OR. Se puede anidar a cualquier profundidad. Inmutable.
 */
final class FilterGroup implements FilterExpression
{
    /** @var list<FilterExpression> */
    public readonly array $children;

    private function __construct(
        public readonly LogicalOperator $operator,
        FilterExpression ...$children,
    ) {
        if ($children === []) {
            throw new InvalidArgumentException('Un FilterGroup requiere al menos un filtro');
        }
        $this->children = array_values($children);
    }

    /** OR entre los elementos. */
    public static function anyOf(FilterExpression ...$children): self
    {
        return new self(LogicalOperator::Or, ...$children);
    }

    /** AND entre los elementos. */
    public static function allOf(FilterExpression ...$children): self
    {
        return new self(LogicalOperator::And, ...$children);
    }
}
