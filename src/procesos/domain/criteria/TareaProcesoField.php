<?php

namespace src\procesos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\procesos\domain\entity\TareaProceso} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TareaProcesoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdTipoProceso = 'id_tipo_proceso';
    case IdFase = 'id_fase';
    case IdTarea = 'id_tarea';
    case Status = 'status';
    case IdOfResponsable = 'id_of_responsable';
}
