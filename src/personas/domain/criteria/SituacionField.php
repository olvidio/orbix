<?php

namespace src\personas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\personas\domain\entity\Situacion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum SituacionField: string implements CriteriaField
{
    case Situacion = 'situacion';
    case NombreSituacion = 'nombre_situacion';
}
