<?php

namespace src\personas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\personas\domain\entity\UltimaAsistencia} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum UltimaAsistenciaField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case IdTipoActiv = 'id_tipo_activ';
    case FIni = 'f_ini';
    case Descripcion = 'descripcion';
    case Cdr = 'cdr';
}
