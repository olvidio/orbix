<?php

namespace src\actividadessacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadessacd\domain\entity\ActividadSacdTexto} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActividadSacdTextoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case Idioma = 'idioma';
    case Clave = 'clave';
    case Texto = 'texto';
}
