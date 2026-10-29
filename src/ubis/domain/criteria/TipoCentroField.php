<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\TipoCentro} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TipoCentroField: string implements CriteriaField
{
    case TipoCtr = 'tipo_ctr';
    case NombreTipoCtr = 'nombre_tipo_ctr';
}
