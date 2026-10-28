<?php

namespace src\casas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\casas\domain\entity\UbiGasto} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum UbiGastoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdUbi = 'id_ubi';
    case FGasto = 'f_gasto';
    case Tipo = 'tipo';
    case Cantidad = 'cantidad';
}
