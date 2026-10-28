<?php

namespace src\utils_database\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\utils_database\domain\entity\DbSchema} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum DbSchemaField: string implements CriteriaField
{
    case Schema = 'schema';
    case Id = 'id';
}
