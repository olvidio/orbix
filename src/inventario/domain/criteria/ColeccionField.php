<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\Coleccion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ColeccionField: string implements CriteriaField
{
    case IdColeccion = 'id_coleccion';
    case NomColeccion = 'nom_coleccion';
    case Agrupar = 'agrupar';
}
