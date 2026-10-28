<?php

namespace src\zonassacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\zonassacd\domain\entity\ZonaSacd} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ZonaSacdField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case IdZona = 'id_zona';
    case Propia = 'propia';
    case Dw1 = 'dw1';
    case Dw2 = 'dw2';
    case Dw3 = 'dw3';
    case Dw4 = 'dw4';
    case Dw5 = 'dw5';
    case Dw6 = 'dw6';
    case Dw7 = 'dw7';
}
