<?php

namespace src\menus\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\menus\domain\entity\GrupMenuRole} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum GrupMenuRoleField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdGrupmenu = 'id_grupmenu';
    case IdRole = 'id_role';
}
