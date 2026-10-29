<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\Delegacion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum DelegacionField: string implements CriteriaField
{
    case IdDl = 'id_dl';
    case Dl = 'dl';
    case Region = 'region';
    case NombreDl = 'nombre_dl';
    case Active = 'active';
    case GrupoEstudios = 'grupo_estudios';
    case RegionStgr = 'region_stgr';
}
