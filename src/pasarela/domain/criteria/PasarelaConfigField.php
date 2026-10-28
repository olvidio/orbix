<?php

namespace src\pasarela\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\pasarela\domain\entity\PasarelaConfig} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 * No incluye `json_valor` (jsonb): no se filtra por él.
 */
enum PasarelaConfigField: string implements CriteriaField
{
    case NomParametro = 'nom_parametro';
}
