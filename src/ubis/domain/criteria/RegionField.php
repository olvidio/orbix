<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\Region} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum RegionField: string implements CriteriaField
{
    case IdRegion = 'id_region';
    case Region = 'region';
    case NombreRegion = 'nombre_region';
    case Active = 'active';
}
