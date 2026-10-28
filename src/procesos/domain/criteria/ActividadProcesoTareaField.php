<?php

namespace src\procesos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\procesos\domain\entity\ActividadProcesoTarea} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActividadProcesoTareaField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdTipoProceso = 'id_tipo_proceso';
    case IdActiv = 'id_activ';
    case IdFase = 'id_fase';
    case IdTarea = 'id_tarea';
    case Completado = 'completado';
    case Observ = 'observ';
}
