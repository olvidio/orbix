<?php
/**
 * Endpoint backend que devuelve el payload necesario (datos de desplegable,
 * tabla HTML o valor escalar) segun el parametro POST `salida`. Usado por las
 * cascadas de filtros de actividades desde callers AJAX (templates Twig/phtml).
 *
 * Responde siempre JSON via web\ContestarJson. Las salidas de tipo desplegable
 * (asistentes, actividad, nom_tipo, dl_org, filtro_lugar, lugar) devuelven
 * bajo `data` el payload {id, opciones, selected, blanco, val_blanco, action}
 * y el frontend construye el `<select>`. Las salidas nom_tipo_tabla e
 * id_tarifa y nivel_stgr_defecto mantienen la forma legacy {content: string}
 * con HTML o valor escalar.
 */

use src\actividades\application\ActividadTipoGetActividad;
use src\actividades\application\ActividadTipoGetAsistentes;
use src\actividades\application\ActividadTipoGetDlOrg;
use src\actividades\application\ActividadTipoGetFiltroLugar;
use src\actividades\application\ActividadTipoGetIdTarifa;
use src\actividades\application\ActividadTipoGetLugar;
use src\actividades\application\ActividadTipoGetNomTipo;
use src\actividades\application\ActividadTipoGetNomTipoTabla;
use src\actividades\application\ActividadTipoGetNivelStgrDefecto;
use web\ContestarJson;

$Qsalida = (string)filter_input(INPUT_POST, 'salida');

// Salidas que devuelven el payload estructurado de un desplegable
// (id, opciones, selected, blanco, val_blanco, action). El frontend construye
// el `<select>`. El payload se envia directamente bajo `data`.
switch ($Qsalida) {
    case 'asistentes':
        ContestarJson::enviar('', (new ActividadTipoGetAsistentes())->execute($_POST));
        exit;
    case 'actividad':
        ContestarJson::enviar('', (new ActividadTipoGetActividad())->execute($_POST));
        exit;
    case 'nom_tipo':
        ContestarJson::enviar('', (new ActividadTipoGetNomTipo())->execute($_POST));
        exit;
    case 'lugar':
        ContestarJson::enviar('', (new ActividadTipoGetLugar())->execute($_POST));
        exit;
    case 'dl_org':
        ContestarJson::enviar('', (new ActividadTipoGetDlOrg())->execute($_POST));
        exit;
    case 'filtro_lugar':
        ContestarJson::enviar('', (new ActividadTipoGetFiltroLugar())->execute($_POST));
        exit;
}

// Salidas que devuelven HTML/valor escalar (bajo `content`).
switch ($Qsalida) {
    case 'nom_tipo_tabla':
        $content = (new ActividadTipoGetNomTipoTabla())->execute($_POST);
        break;
    case 'id_tarifa':
        $content = (new ActividadTipoGetIdTarifa())->execute($_POST);
        break;
    case 'nivel_stgr_defecto':
        $content = (new ActividadTipoGetNivelStgrDefecto())->execute($_POST);
        break;
    default:
        ContestarJson::enviar(sprintf(_('opción no definida: salida=%s'), $Qsalida));
        exit;
}

ContestarJson::enviar('', ['content' => (string)$content]);
