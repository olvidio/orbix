<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

use BackedEnum;

/**
 * Campo consultable de una entidad. Lo implementan los enums `<Entidad>Field`
 * de cada módulo (`src/<modulo>/domain/criteria/`); el valor del enum es el
 * nombre del campo en la entidad, no la columna. El enum hace de lista blanca.
 */
interface CriteriaField extends BackedEnum
{
}
