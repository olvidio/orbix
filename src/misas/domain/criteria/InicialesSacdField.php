<?php

namespace src\misas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\misas\domain\entity\InicialesSacd} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum InicialesSacdField: string implements CriteriaField
{
    case IdNom = 'id_nom';
    case Iniciales = 'iniciales';
    case Color = 'color';
}
