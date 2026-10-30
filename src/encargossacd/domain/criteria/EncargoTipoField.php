<?php

namespace src\encargossacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\encargossacd\domain\entity\EncargoTipo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EncargoTipoField: string implements CriteriaField
{
    case IdTipoEnc = 'id_tipo_enc';
    case TipoEnc = 'tipo_enc';
    case ModHorario = 'mod_horario';
}
