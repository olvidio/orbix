<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * Interpreta valores booleanos de PostgreSQL/PHP en vistas y controladores frontend.
 */
function is_true(mixed $val): ?bool
{
    return \src\shared\domain\helpers\FuncTablasSupport::isTrue($val);
}
