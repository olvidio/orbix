<?php

namespace src\asistentes\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\asistentes\domain\entity\Asistente} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 */
enum AsistenteField: string implements CriteriaField
{
    case IdActiv = 'id_activ';
    case IdNom = 'id_nom';
    case Propio = 'propio';
    case EstOk = 'est_ok';
    case Cfi = 'cfi';
    case CfiCon = 'cfi_con';
    case Falta = 'falta';
    case Encargo = 'encargo';
    case DlResponsable = 'dl_responsable';
    case Observ = 'observ';
    case IdTabla = 'id_tabla';
    case Plaza = 'plaza';
    case Propietario = 'propietario';
    case ObservEst = 'observ_est';
    case Cama = 'cama';
}
