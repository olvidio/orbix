<?php

namespace src\actividadcargos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadcargos\domain\entity\Cargo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CargoField: string implements CriteriaField
{
    case IdCargo = 'id_cargo';
    case Cargo = 'cargo';
    case OrdenCargo = 'orden_cargo';
    case Sf = 'sf';
    case Sv = 'sv';
    case TipoCargo = 'tipo_cargo';
}
