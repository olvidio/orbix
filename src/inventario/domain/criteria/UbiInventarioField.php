<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\UbiInventario} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum UbiInventarioField: string implements CriteriaField
{
    case IdUbi = 'id_ubi';
    case NomUbi = 'nom_ubi';
    case IdUbiActiv = 'id_ubi_activ';
}
