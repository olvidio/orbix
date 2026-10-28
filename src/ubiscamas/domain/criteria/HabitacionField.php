<?php

namespace src\ubiscamas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubiscamas\domain\entity\Habitacion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 * No incluye `tipoLavabo`: su columna no se escribe igual que la propiedad y nadie busca por ella.
 */
enum HabitacionField: string implements CriteriaField
{
    case IdSchema = 'id_schema';
    case IdUbi = 'id_ubi';
    case IdHabitacion = 'id_habitacion';
    case Orden = 'orden';
    case Nombre = 'nombre';
    case NumeroCamas = 'numero_camas';
    case NumeroCamasVip = 'numero_camas_vip';
    case Planta = 'planta';
    case Sillon = 'sillon';
    case Adaptada = 'adaptada';
    case Observaciones = 'observaciones';
    case Despacho = 'despacho';
}
