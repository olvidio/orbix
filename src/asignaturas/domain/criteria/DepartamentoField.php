<?php

namespace src\asignaturas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\asignaturas\domain\entity\Departamento} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum DepartamentoField: string implements CriteriaField
{
    case IdDepartamento = 'id_departamento';
    case NombreDepartamento = 'nombre_departamento';
}
