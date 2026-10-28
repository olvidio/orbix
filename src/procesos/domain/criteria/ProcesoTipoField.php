<?php

namespace src\procesos\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\procesos\domain\entity\ProcesoTipo} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ProcesoTipoField: string implements CriteriaField
{
    case IdTipoProceso = 'id_tipo_proceso';
    case NomProceso = 'nom_proceso';
    case Sfsv = 'sfsv';
}
