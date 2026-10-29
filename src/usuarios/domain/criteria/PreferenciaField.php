<?php

namespace src\usuarios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\usuarios\domain\entity\Preferencia} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum PreferenciaField: string implements CriteriaField
{
    case Tipo = 'tipo';
    case Preferencia = 'preferencia';
    case IdUsuario = 'id_usuario';
}
