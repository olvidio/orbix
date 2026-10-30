<?php

namespace src\misas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\misas\domain\entity\Plantilla} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum PlantillaField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdCtr = 'id_ctr';
    case Tarea = 'tarea';
    case Dia = 'dia';
    case Semana = 'semana';
    case TStart = 't_start';
    case TEnd = 't_end';
    case IdNom = 'id_nom';
    case Observ = 'observ';
}
