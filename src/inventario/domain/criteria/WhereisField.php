<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\Whereis} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum WhereisField: string implements CriteriaField
{
    case IdItemWhereis = 'id_item_whereis';
    case IdItemEgm = 'id_item_egm';
    case IdDoc = 'id_doc';
}
