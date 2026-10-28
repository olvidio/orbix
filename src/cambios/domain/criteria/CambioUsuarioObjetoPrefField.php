<?php

namespace src\cambios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\cambios\domain\entity\CambioUsuarioObjetoPref} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CambioUsuarioObjetoPrefField: string implements CriteriaField
{
    case IdItemUsuarioObjeto = 'id_item_usuario_objeto';
    case IdUsuario = 'id_usuario';
    case DlOrg = 'dl_org';
    case IdTipoActivTxt = 'id_tipo_activ_txt';
    case IdFaseRef = 'id_fase_ref';
    case AvisoOff = 'aviso_off';
    case AvisoOn = 'aviso_on';
    case AvisoOutdate = 'aviso_outdate';
    case Objeto = 'objeto';
    case AvisoTipo = 'aviso_tipo';
    case CsvIdPau = 'csv_id_pau';
}
