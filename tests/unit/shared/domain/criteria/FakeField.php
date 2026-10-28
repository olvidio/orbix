<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

enum FakeField: string implements CriteriaField
{
    case Id = 'id';
    case Nombre = 'nombre';
    case FIni = 'f_ini';
}
