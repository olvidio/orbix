<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\TelecoUbi} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TelecoUbiField: string implements CriteriaField
{
    case IdUbi = 'id_ubi';
    case IdTipoTeleco = 'id_tipo_teleco';
    case IdDescTeleco = 'id_desc_teleco';
    case NumTeleco = 'num_teleco';
    case Observ = 'observ';
    case IdItem = 'id_item';
}
