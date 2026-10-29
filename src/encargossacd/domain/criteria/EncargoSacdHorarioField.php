<?php

namespace src\encargossacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\encargossacd\domain\entity\EncargoSacdHorario} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EncargoSacdHorarioField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdEnc = 'id_enc';
    case IdNom = 'id_nom';
    case FIni = 'f_ini';
    case FFin = 'f_fin';
    case DiaRef = 'dia_ref';
    case DiaNum = 'dia_num';
    case MasMenos = 'mas_menos';
    case DiaInc = 'dia_inc';
    case HIni = 'h_ini';
    case HFin = 'h_fin';
    case IdItemTareaSacd = 'id_item_tarea_sacd';
}
