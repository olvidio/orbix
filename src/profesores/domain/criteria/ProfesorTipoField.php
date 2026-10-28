<?php

namespace src\profesores\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\profesores\domain\entity\ProfesorTipo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ProfesorTipoField: string implements CriteriaField
{
    case IdTipoProfesor = 'id_tipo_profesor';
    case TipoProfesor = 'tipo_profesor';
}
