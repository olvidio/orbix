<?php

namespace src\personas\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\personas\domain\entity\Traslado} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum TrasladoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case FTraslado = 'f_traslado';
    case TipoCmb = 'tipo_cmb';
    case IdCtrOrigen = 'id_ctr_origen';
    case CtrOrigen = 'ctr_origen';
    case IdCtrDestino = 'id_ctr_destino';
    case CtrDestino = 'ctr_destino';
    case Observ = 'observ';
}
