<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\CriteriaField;

/** Campos de la tabla temporal `t_criteria` que usan los tests de Criteria. */
enum TablaField: string implements CriteriaField
{
    case Id = 'id';
    case Nombre = 'nombre';
    case FIni = 'f_ini';
    case Bits = 'bits';
    case Tags = 'tags';
    case Cod = 'cod';
}
