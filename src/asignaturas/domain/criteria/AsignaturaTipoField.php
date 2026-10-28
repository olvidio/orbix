<?php

namespace src\asignaturas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\asignaturas\domain\entity\AsignaturaTipo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 * No incluye `year`: su columna es `año` y nadie busca por ella.
 */
enum AsignaturaTipoField: string implements CriteriaField
{
    case IdTipo = 'id_tipo';
    case TipoAsignatura = 'tipo_asignatura';
    case TipoBreve = 'tipo_breve';
    case TipoLatin = 'tipo_latin';
}
