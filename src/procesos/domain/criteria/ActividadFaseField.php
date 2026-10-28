<?php

namespace src\procesos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\procesos\domain\entity\ActividadFase} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActividadFaseField: string implements CriteriaField
{
    case IdFase = 'id_fase';
    case DescFase = 'desc_fase';
    case Sf = 'sf';
    case Sv = 'sv';
}
