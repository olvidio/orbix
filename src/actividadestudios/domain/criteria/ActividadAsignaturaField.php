<?php

namespace src\actividadestudios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadestudios\domain\entity\ActividadAsignatura} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActividadAsignaturaField: string implements CriteriaField
{
    case IdSchema = 'id_schema';
    case IdActiv = 'id_activ';
    case IdAsignatura = 'id_asignatura';
    case IdProfesor = 'id_profesor';
    case AvisProfesor = 'avis_profesor';
    case Tipo = 'tipo';
    case FIni = 'f_ini';
    case FFin = 'f_fin';
}
