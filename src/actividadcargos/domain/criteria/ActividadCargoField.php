<?php

namespace src\actividadcargos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadcargos\domain\entity\ActividadCargo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActividadCargoField: string implements CriteriaField
{
    case IdSchema = 'id_schema';
    case IdActiv = 'id_activ';
    case IdCargo = 'id_cargo';
    case IdNom = 'id_nom';
    case PuedeAgd = 'puede_agd';
    case Observ = 'observ';
    case IdItem = 'id_item';
}
