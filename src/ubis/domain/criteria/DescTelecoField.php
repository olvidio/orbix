<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\DescTeleco} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum DescTelecoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case Orden = 'orden';
    case IdTipoTeleco = 'id_tipo_teleco';
    case DescTeleco = 'desc_teleco';
    case Ubi = 'ubi';
    case Persona = 'persona';
}
