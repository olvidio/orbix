<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\CasaPeriodo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CasaPeriodoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdUbi = 'id_ubi';
    case FIni = 'f_ini';
    case FFin = 'f_fin';
    case Sfsv = 'sfsv';
}
