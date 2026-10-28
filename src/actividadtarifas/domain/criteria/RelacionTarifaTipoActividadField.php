<?php

namespace src\actividadtarifas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum RelacionTarifaTipoActividadField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdTarifa = 'id_tarifa';
    case IdTipoActiv = 'id_tipo_activ';
    case IdSerie = 'id_serie';
}
