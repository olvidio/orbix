<?php

namespace src\actividades\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividades\domain\entity\Importada} por los que se puede buscar u ordenar.
 */
enum ImportadaField: string implements CriteriaField
{
    case IdActiv = 'id_activ';
}
