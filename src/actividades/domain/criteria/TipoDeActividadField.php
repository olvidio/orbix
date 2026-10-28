<?php

namespace src\actividades\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividades\domain\entity\TipoDeActividad} por los que se puede buscar u ordenar.
 */
enum TipoDeActividadField: string implements CriteriaField
{
    case IdTipoActiv = 'id_tipo_activ';
    case Nombre = 'nombre';
    case IdTipoProcesoSv = 'id_tipo_proceso_sv';
    case IdTipoProcesoExSv = 'id_tipo_proceso_ex_sv';
    case IdTipoProcesoSf = 'id_tipo_proceso_sf';
    case IdTipoProcesoExSf = 'id_tipo_proceso_ex_sf';
}
