<?php

namespace src\encargossacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\encargossacd\domain\entity\EncargoTexto} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EncargoTextoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case Idioma = 'idioma';
    case Clave = 'clave';
    case Texto = 'texto';
}
