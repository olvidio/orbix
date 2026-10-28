<?php

namespace src\profesores\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\profesores\domain\entity\ProfesorCongreso} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum ProfesorCongresoField: string implements CriteriaField
{
    case IdItem = 'id_item';
    case IdNom = 'id_nom';
    case Congreso = 'congreso';
    case Lugar = 'lugar';
    case FIni = 'f_ini';
    case FFin = 'f_fin';
    case Organiza = 'organiza';
    case Tipo = 'tipo';
}
