<?php

namespace src\actividadplazas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadplazas\domain\entity\PlazaPeticion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum PlazaPeticionField: string implements CriteriaField
{
    case IdNom = 'id_nom';
    case IdActiv = 'id_activ';
    case Orden = 'orden';
    case Tipo = 'tipo';
}
