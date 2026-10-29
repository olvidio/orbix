<?php

namespace src\notas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\notas\domain\entity\Nota} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum NotaField: string implements CriteriaField
{
    case IdSituacion = 'id_situacion';
    case Descripcion = 'descripcion';
    case Superada = 'superada';
    case Breve = 'breve';
}
