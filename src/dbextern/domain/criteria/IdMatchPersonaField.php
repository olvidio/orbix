<?php

namespace src\dbextern\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\dbextern\domain\entity\IdMatchPersona} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum IdMatchPersonaField: string implements CriteriaField
{
    case IdListas = 'id_listas';
    case IdOrbix = 'id_orbix';
    case IdTabla = 'id_tabla';
}
