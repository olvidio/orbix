<?php

namespace src\misas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\misas\domain\entity\EncargoDia} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EncargoDiaField: string implements CriteriaField
{
    case UuidItem = 'uuid_item';
    case IdEnc = 'id_enc';
    case Tstart = 'tstart';
    case Tend = 'tend';
    case IdNom = 'id_nom';
    case Observ = 'observ';
    case Status = 'status';
}
