<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\Egm} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EgmField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdEquipaje = 'id_equipaje';
    case IdGrupo = 'id_grupo';
    case IdLugar = 'id_lugar';
    case Texto = 'texto';
}
