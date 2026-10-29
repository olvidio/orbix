<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\CentroEx} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum CentroExField: string implements CriteriaField
{
    case TipoUbi = 'tipo_ubi';
    case IdUbi = 'id_ubi';
    case NombreUbi = 'nombre_ubi';
    case Dl = 'dl';
    case Pais = 'pais';
    case Region = 'region';
    case Active = 'active';
    case FActive = 'f_active';
    case Sv = 'sv';
    case Sf = 'sf';
    case TipoCtr = 'tipo_ctr';
    case TipoLabor = 'tipo_labor';
    case Cdc = 'cdc';
    case IdCtrPadre = 'id_ctr_padre';
    case IdAuto = 'id_auto';
}
