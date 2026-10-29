<?php

namespace src\ubis\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\ubis\domain\entity\Direccion} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum DireccionField: string implements CriteriaField
{
    case IdDireccion = 'id_direccion';
    case Direccion = 'direccion';
    case CP = 'c_p';
    case Poblacion = 'poblacion';
    case Provincia = 'provincia';
    case AP = 'a_p';
    case Pais = 'pais';
    case FDireccion = 'f_direccion';
    case Observ = 'observ';
    case CpDcha = 'cp_dcha';
    case Latitud = 'latitud';
    case Longitud = 'longitud';
    case PlanoDoc = 'plano_doc';
    case PlanoExtension = 'plano_extension';
    case PlanoNom = 'plano_nom';
    case NomSede = 'nom_sede';
}
