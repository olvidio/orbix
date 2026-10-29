<?php

namespace src\notas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\notas\domain\entity\ActaTribunal} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActaTribunalField: string implements CriteriaField
{
    case Acta = 'acta';
    case Examinador = 'examinador';
    case Orden = 'orden';
    case IdItem = 'id_item';
}
