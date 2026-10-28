<?php

namespace src\actividadescentro\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividadescentro\domain\entity\CentroEncargado} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CentroEncargadoField: string implements CriteriaField
{
    case IdActiv = 'id_activ';
    case IdUbi = 'id_ubi';
    case NumOrden = 'num_orden';
    case Encargo = 'encargo';
}
