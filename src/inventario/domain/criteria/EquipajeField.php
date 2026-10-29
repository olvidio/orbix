<?php

namespace src\inventario\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\inventario\domain\entity\Equipaje} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum EquipajeField: string implements CriteriaField
{
    case IdEquipaje = 'id_equipaje';
    case IdsActiv = 'ids_activ';
    case Lugar = 'lugar';
    case FIni = 'f_ini';
    case FFin = 'f_fin';
    case IdUbiActiv = 'id_ubi_activ';
    case NomEquipaje = 'nom_equipaje';
    case Cabecera = 'cabecera';
    case Pie = 'pie';
    case Cabecerab = 'cabecerab';
}
