<?php

namespace src\procesos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\procesos\domain\entity\ActividadTarea} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActividadTareaField: string implements CriteriaField
{
    case IdFase = 'id_fase';
    case IdTarea = 'id_tarea';
    case DescTarea = 'desc_tarea';
}
