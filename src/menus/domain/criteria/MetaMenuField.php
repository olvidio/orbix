<?php

namespace src\menus\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\menus\domain\entity\MetaMenu} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum MetaMenuField: string implements CriteriaField
{
    case IdMetamenu = 'id_metamenu';
    case IdMod = 'id_mod';
    case Url = 'url';
    case Parametros = 'parametros';
    case Descripcion = 'descripcion';
}
