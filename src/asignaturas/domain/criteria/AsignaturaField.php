<?php

namespace src\asignaturas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\asignaturas\domain\entity\Asignatura} por los que se puede buscar u ordenar.
 * El valor es el nombre de la columna (coincide con el getter `getNombre_asignatura()` de la entidad,
 * cuya propiedad privada se llama `nombre_signatura`).
 * `PlanEstudios` es una columna `integer[]`: se filtra con `Filter::arrayContains`.
 */
enum AsignaturaField: string implements CriteriaField
{
    case IdAsignatura = 'id_asignatura';
    case IdNivel = 'id_nivel';
    case NombreAsignatura = 'nombre_asignatura';
    case NombreCorto = 'nombre_corto';
    case Creditos = 'creditos';
    case Year = 'year';
    case IdSector = 'id_sector';
    case Active = 'active';
    case IdTipo = 'id_tipo';
    case PlanEstudios = 'plan_estudios';
}
