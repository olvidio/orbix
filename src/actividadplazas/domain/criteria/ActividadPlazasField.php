<?php

namespace src\actividadplazas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadplazas\domain\entity\ActividadPlazas} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ActividadPlazasField: string implements CriteriaField
{
    case IdActiv = 'id_activ';
    case IdDl = 'id_dl';
    case Plazas = 'plazas';
    case Cl = 'cl';
    case DlTabla = 'dl_tabla';
}
