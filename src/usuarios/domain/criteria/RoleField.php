<?php

namespace src\usuarios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\usuarios\domain\entity\Role} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum RoleField: string implements CriteriaField
{
    case IdRole = 'id_role';
    case Role = 'role';
    case Sf = 'sf';
    case Sv = 'sv';
    case Pau = 'pau';
}
