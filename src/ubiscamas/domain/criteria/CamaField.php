<?php

namespace src\ubiscamas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubiscamas\domain\entity\Cama} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CamaField: string implements CriteriaField
{
    case IdSchema = 'id_schema';
    case IdCama = 'id_cama';
    case IdHabitacion = 'id_habitacion';
    case Descripcion = 'descripcion';
    case Larga = 'larga';
    case Vip = 'vip';
}
