<?php

namespace src\profesores\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\profesores\domain\entity\ProfesorDocenciaStgr} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ProfesorDocenciaStgrField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case IdAsignatura = 'id_asignatura';
    case IdActiv = 'id_activ';
    case Tipo = 'tipo';
    case CursoInicio = 'curso_inicio';
    case Acta = 'acta';
}
