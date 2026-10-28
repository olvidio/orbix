<?php

namespace src\asignaturas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\asignaturas\domain\entity\Sector} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum SectorField: string implements CriteriaField
{
    case IdSector = 'id_sector';
    case IdDepartamento = 'id_departamento';
    case NombreSector = 'nombre_sector';
}
