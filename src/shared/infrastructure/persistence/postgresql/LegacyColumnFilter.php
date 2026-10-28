<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\FilterExpression;
use src\shared\domain\criteria\Operator;

/**
 * Filtro sobre una columna que no tiene campo en el enum del repositorio. Se traduce igual
 * que un {@see \src\shared\domain\criteria\Filter}, pero sin la lista blanca del enum
 * (el nombre de columna solo se valida como identificador).
 *
 * @internal Solo lo construye {@see LegacyWhereAdapter}; desaparece con él (paso 5.2).
 */
final class LegacyColumnFilter implements FilterExpression
{
    public function __construct(
        public readonly string $column,
        public readonly Operator $operator,
        public readonly mixed $value = null,
    ) {
    }
}
