<?php

namespace src\actividadtarifas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadtarifas\domain\entity\TipoTarifa} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TipoTarifaField: string implements CriteriaField
{
    case IdTarifa = 'id_tarifa';
    case Modo = 'modo';
    case Letra = 'letra';
    case Sfsv = 'sfsv';
    case Observ = 'observ';
}
