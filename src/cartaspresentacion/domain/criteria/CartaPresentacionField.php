<?php

namespace src\cartaspresentacion\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\cartaspresentacion\domain\entity\CartaPresentacion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CartaPresentacionField: string implements CriteriaField
{
    case IdDireccion = 'id_direccion';
    case IdUbi = 'id_ubi';
    case PresNom = 'pres_nom';
    case PresTelf = 'pres_telf';
    case PresMail = 'pres_mail';
    case Zona = 'zona';
    case Observ = 'observ';
}
