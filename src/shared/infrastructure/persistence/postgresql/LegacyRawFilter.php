<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\FilterExpression;

/**
 * Filtro con SQL crudo (operadores `TXT`, `~INV`, `LIKE`…) que no tienen equivalente en
 * {@see \src\shared\domain\criteria\Operator}. Los `?` del SQL se enlazan en orden con
 * `$values`; sin valores, el SQL se usa tal cual.
 *
 * @internal Solo lo construye {@see LegacyWhereAdapter}; desaparece con él (paso 5.2).
 */
final class LegacyRawFilter implements FilterExpression
{
    /** @param list<mixed> $values */
    public function __construct(
        public readonly string $sql,
        public readonly array $values = [],
    ) {
    }
}
