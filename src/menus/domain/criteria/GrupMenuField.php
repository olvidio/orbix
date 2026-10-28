<?php

namespace src\menus\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\menus\domain\entity\GrupMenu} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum GrupMenuField: string implements CriteriaField
{
    case IdGrupmenu = 'id_grupmenu';
    case GrupMenu = 'grup_menu';
    case Orden = 'orden';
}
