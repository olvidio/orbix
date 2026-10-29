<?php

namespace src\actividadestudios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadestudios\domain\entity\Matricula} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum MatriculaField: string implements CriteriaField
{
    case IdActiv = 'id_activ';
    case IdAsignatura = 'id_asignatura';
    case IdNom = 'id_nom';
    case IdSituacion = 'id_situacion';
    case Preceptor = 'preceptor';
    case IdNivel = 'id_nivel';
    case NotaNum = 'nota_num';
    case NotaMax = 'nota_max';
    case IdPreceptor = 'id_preceptor';
    case Acta = 'acta';
    case IdDl = 'id_dl';
    case IdSchema = 'id_schema';
}
