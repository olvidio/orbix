<?php

namespace src\menus\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\menus\domain\entity\MenuDb} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum MenuDbField: string implements CriteriaField
{
    case IdMenu = 'id_menu';
    case Orden = 'orden';
    case Menu = 'menu';
    case Parametros = 'parametros';
    case IdMetamenu = 'id_metamenu';
    case MenuPerm = 'menu_perm';
    case IdGrupmenu = 'id_grupmenu';
    case Ok = 'ok';
}
