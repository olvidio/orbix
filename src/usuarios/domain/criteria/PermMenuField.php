<?php

namespace src\usuarios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\usuarios\domain\entity\PermMenu} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum PermMenuField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdUsuario = 'id_usuario';
    case MenuPerm = 'menu_perm';
}
