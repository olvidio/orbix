<?php

namespace src\notas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\notas\domain\entity\PersonaNota} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 * Lo comparten `PersonaNota` y `PersonaNotaOtraRegionStgr` (misma tabla padre `e_notas`; el jsonb `json_certificados` no es buscable).
 */
enum PersonaNotaField: string implements CriteriaField
{
    case IdSchema = 'id_schema';
    case IdNom = 'id_nom';
    case IdNivel = 'id_nivel';
    case IdAsignatura = 'id_asignatura';
    case IdSituacion = 'id_situacion';
    case Acta = 'acta';
    case FActa = 'f_acta';
    case Detalle = 'detalle';
    case Preceptor = 'preceptor';
    case IdPreceptor = 'id_preceptor';
    case Epoca = 'epoca';
    case IdActiv = 'id_activ';
    case NotaNum = 'nota_num';
    case NotaMax = 'nota_max';
    case TipoActa = 'tipo_acta';
}
