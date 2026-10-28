<?php

namespace src\zonassacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\zonassacd\domain\entity\ZonaGrupo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ZonaGrupoField: string implements CriteriaField
{
    case IdGrupo = 'id_grupo';
    case NombreGrupo = 'nombre_grupo';
    case Orden = 'orden';
}
