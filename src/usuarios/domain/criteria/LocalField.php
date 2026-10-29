<?php

namespace src\usuarios\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\usuarios\domain\entity\Local} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum LocalField: string implements CriteriaField
{
    case IdLocale = 'id_locale';
    case NomLocale = 'nom_locale';
    case Idioma = 'idioma';
    case NomIdioma = 'nom_idioma';
    case Active = 'active';
}
