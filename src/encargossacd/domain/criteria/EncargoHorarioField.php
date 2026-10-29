<?php

namespace src\encargossacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\encargossacd\domain\entity\EncargoHorario} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EncargoHorarioField: string implements CriteriaField
{
    case IdEnc = 'id_enc';
    case IdItemH = 'id_item_h';
    case FIni = 'f_ini';
    case FFin = 'f_fin';
    case DiaRef = 'dia_ref';
    case DiaNum = 'dia_num';
    case MasMenos = 'mas_menos';
    case DiaInc = 'dia_inc';
    case HIni = 'h_ini';
    case HFin = 'h_fin';
    case NSacd = 'n_sacd';
    case Mes = 'mes';
}
