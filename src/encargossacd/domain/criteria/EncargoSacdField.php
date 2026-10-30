<?php

namespace src\encargossacd\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\encargossacd\domain\entity\EncargoSacd} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EncargoSacdField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdEnc = 'id_enc';
    case IdNom = 'id_nom';
    case Modo = 'modo';
    case FIni = 'f_ini';
    case FFin = 'f_fin';
}
