<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\Lugar} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum LugarField: string implements CriteriaField
{
    case IdLugar = 'id_lugar';
    case IdUbi = 'id_ubi';
    case NomLugar = 'nom_lugar';
}
