<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\TipoCasa} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TipoCasaField: string implements CriteriaField
{
    case TipoCasa = 'tipo_casa';
    case NombreTipoCasa = 'nombre_tipo_casa';
}
