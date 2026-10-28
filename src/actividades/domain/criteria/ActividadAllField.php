<?php

namespace src\actividades\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

/**
 * Campos de {@see \src\actividades\domain\entity\ActividadAll} por los que se puede buscar u ordenar.
 * El valor es el nombre del campo en la entidad; el mapa a columna vive en el repositorio.
 * Lo comparten los repositorios de actividades (`Dl`, `Ex`, `Pub` y el de la vista `av_actividades`).
 */
enum ActividadAllField: string implements CriteriaField
{
    case IdAuto = 'id_auto';
    case IdActiv = 'id_activ';
    case IdTipoActiv = 'id_tipo_activ';
    case DlOrg = 'dl_org';
    case NomActiv = 'nom_activ';
    case IdUbi = 'id_ubi';
    case DescActiv = 'desc_activ';
    case FIni = 'f_ini';
    case HIni = 'h_ini';
    case FFin = 'f_fin';
    case HFin = 'h_fin';
    case TipoHorario = 'tipo_horario';
    case Precio = 'precio';
    case NumAsistentes = 'num_asistentes';
    case Status = 'status';
    case Observ = 'observ';
    case NivelStgr = 'nivel_stgr';
    case ObservMaterial = 'observ_material';
    case LugarEsp = 'lugar_esp';
    case Tarifa = 'tarifa';
    case IdRepeticion = 'id_repeticion';
    case Publicado = 'publicado';
    case IdTabla = 'id_tabla';
    case Plazas = 'plazas';
    case Idioma = 'idioma';
}
