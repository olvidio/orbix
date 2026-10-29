<?php

namespace src\usuarios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\usuarios\domain\entity\Grupo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum GrupoField: string implements CriteriaField
{
    case IdUsuario = 'id_usuario';
    case Usuario = 'usuario';
    case IdRole = 'id_role';
}
