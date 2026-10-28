<?php

namespace src\dossiers\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\dossiers\domain\entity\Dossier} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum DossierField: string implements CriteriaField
{
    case Tabla = 'tabla';
    case IdPau = 'id_pau';
    case IdTipoDossier = 'id_tipo_dossier';
    case FIni = 'f_ini';
    case FCambDossier = 'f_camb_dossier';
    case Active = 'active';
    case FActive = 'f_active';
}
