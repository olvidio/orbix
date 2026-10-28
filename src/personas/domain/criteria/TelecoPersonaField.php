<?php

namespace src\personas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\personas\domain\entity\TelecoPersona} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TelecoPersonaField: string implements CriteriaField
{
    case IdNom = 'id_nom';
    case IdItem = 'id_item';
    case IdTipoTeleco = 'id_tipo_teleco';
    case NumTeleco = 'num_teleco';
    case Observ = 'observ';
    case IdDescTeleco = 'id_desc_teleco';
}
