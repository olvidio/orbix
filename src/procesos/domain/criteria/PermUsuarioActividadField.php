<?php

namespace src\procesos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\procesos\domain\entity\PermUsuarioActividad} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum PermUsuarioActividadField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdUsuario = 'id_usuario';
    case DlPropia = 'dl_propia';
    case IdTipoActivTxt = 'id_tipo_activ_txt';
    case FaseRef = 'fase_ref';
    case AfectaA = 'afecta_a';
    case PermOn = 'perm_on';
    case PermOff = 'perm_off';
}
