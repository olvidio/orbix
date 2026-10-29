<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\TarifaUbi} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TarifaUbiField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdUbi = 'id_ubi';
    case IdTarifa = 'id_tarifa';
    case Year = 'year';
    case Cantidad = 'cantidad';
    case Observ = 'observ';
    case IdSerie = 'id_serie';
}
