<?php

namespace src\zonassacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\zonassacd\domain\entity\Zona} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ZonaField: string implements CriteriaField
{
    case IdZona = 'id_zona';
    case NombreZona = 'nombre_zona';
    case Orden = 'orden';
    case IdGrupo = 'id_grupo';
    case IdNom = 'id_nom';
}
