<?php

namespace src\actividades\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividades\domain\entity\Repeticion} por los que se puede buscar u ordenar.
 */
enum RepeticionField: string implements CriteriaField
{
    case IdRepeticion = 'id_repeticion';
    case Repeticion = 'repeticion';
    case Temporada = 'temporada';
    case Tipo = 'tipo';
}
