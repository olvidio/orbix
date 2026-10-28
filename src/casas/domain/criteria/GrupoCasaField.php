<?php

namespace src\casas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\casas\domain\entity\GrupoCasa} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum GrupoCasaField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdUbiPadre = 'id_ubi_padre';
    case IdUbiHijo = 'id_ubi_hijo';
}
