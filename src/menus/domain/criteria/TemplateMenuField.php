<?php

namespace src\menus\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\menus\domain\entity\TemplateMenu} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TemplateMenuField: string implements CriteriaField
{
    case IdTemplateMenu = 'id_template_menu';
    case Nombre = 'nombre';
}
