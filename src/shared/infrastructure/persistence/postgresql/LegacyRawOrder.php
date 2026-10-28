<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\OrderExpression;

/**
 * Trozo de `_ordre` que no es `columna [ASC|DESC]` de un campo conocido (expresiones,
 * funciones, columnas sin campo en el enum). Se emite tal cual.
 *
 * @internal Solo lo construye {@see LegacyWhereAdapter}; desaparece con él (paso 5.2).
 */
final class LegacyRawOrder implements OrderExpression
{
    public function __construct(public readonly string $sql)
    {
    }
}
