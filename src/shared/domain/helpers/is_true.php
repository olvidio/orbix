<?php

declare(strict_types=1);

namespace src\shared\domain\helpers;

/**
 * Interpreta valores booleanos de PostgreSQL/PHP (p. ej. `'t'`, `'true'`, `true`).
 */
function is_true(mixed $val): ?bool
{
    return FuncTablasSupport::isTrue($val);
}
