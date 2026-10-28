<?php

namespace src\configuracion\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\configuracion\domain\entity\App} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum AppField: string implements CriteriaField
{
    case IdApp = 'id_app';
    case Nom = 'nom';
}
