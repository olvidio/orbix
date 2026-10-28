<?php

namespace src\configuracion\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\configuracion\domain\entity\ModuloInstalado} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ModuloInstaladoField: string implements CriteriaField
{
    case IdMod = 'id_mod';
    case Active = 'active';
}
