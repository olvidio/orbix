<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

/**
 * Criterio de ordenación: campo + dirección (+ posición de nulos opcional). Inmutable.
 */
final class Order
{
    public function __construct(
        public readonly CriteriaField $field,
        public readonly OrderDirection $direction = OrderDirection::Asc,
        public readonly ?NullsPosition $nulls = null,
    ) {
    }
}
