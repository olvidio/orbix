<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\TipoTeleco} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TipoTelecoField: string implements CriteriaField
{
    case TipoTeleco = 'tipo_teleco';
    case NombreTeleco = 'nombre_teleco';
    case Ubi = 'ubi';
    case Persona = 'persona';
    case Id = 'id';
}
