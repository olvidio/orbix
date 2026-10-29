<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\Documento} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum DocumentoField: string implements CriteriaField
{
    case IdDoc = 'id_doc';
    case IdTipoDoc = 'id_tipo_doc';
    case IdUbi = 'id_ubi';
    case IdLugar = 'id_lugar';
    case FRecibido = 'f_recibido';
    case FAsignado = 'f_asignado';
    case Observ = 'observ';
    case ObservCtr = 'observ_ctr';
    case FUltComprobacion = 'f_ult_comprobacion';
    case EnBusqueda = 'en_busqueda';
    case Perdido = 'perdido';
    case FPerdido = 'f_perdido';
    case Eliminado = 'eliminado';
    case FEliminado = 'f_eliminado';
    case NumReg = 'num_reg';
    case NumIni = 'num_ini';
    case NumFin = 'num_fin';
    case NumEjemplares = 'num_ejemplares';
}
