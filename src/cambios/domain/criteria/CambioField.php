<?php

namespace src\cambios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\cambios\domain\entity\Cambio} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 * Se omiten `json_fases_sv` y `json_fases_sf` (jsonb: no se filtra por ellos).
 */
enum CambioField: string implements CriteriaField
{
    case IdSchema = 'id_schema';
    case IdItemCambio = 'id_item_cambio';
    case IdTipoCambio = 'id_tipo_cambio';
    case IdActiv = 'id_activ';
    case IdTipoActiv = 'id_tipo_activ';
    case IdStatus = 'id_status';
    case DlOrg = 'dl_org';
    case Objeto = 'objeto';
    case Propiedad = 'propiedad';
    case ValorOld = 'valor_old';
    case ValorNew = 'valor_new';
    case QuienCambia = 'quien_cambia';
    case SfsvQuienCambia = 'sfsv_quien_cambia';
    case TimestampCambio = 'timestamp_cambio';
}
