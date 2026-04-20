<?php
/**
 * Endpoint backend que devuelve un partial (desplegable, tabla o valor
 * escalar como id_tarifa) segun el parametro POST `salida`. Usado para
 * cascadas de filtros de actividades por callers AJAX (templates Twig/phtml).
 *
 * Responde siempre JSON via web\ContestarJson con la clave `content`. Cada
 * accion del switch delega en su use case en src\actividades\application.
 */

use src\actividades\application\ActividadTipoGetActividad;
use src\actividades\application\ActividadTipoGetAsistentes;
use src\actividades\application\ActividadTipoGetDlOrg;
use src\actividades\application\ActividadTipoGetFiltroLugar;
use src\actividades\application\ActividadTipoGetIdTarifa;
use src\actividades\application\ActividadTipoGetLugar;
use src\actividades\application\ActividadTipoGetNomTipo;
use src\actividades\application\ActividadTipoGetNomTipoTabla;
use web\ContestarJson;

$Qsalida = (string)filter_input(INPUT_POST, 'salida');

switch ($Qsalida) {
    case 'asistentes':
        $content = (new ActividadTipoGetAsistentes())->execute($_POST);
        break;
    case 'actividad':
        $content = (new ActividadTipoGetActividad())->execute($_POST);
        break;
    case 'nom_tipo':
        $content = (new ActividadTipoGetNomTipo())->execute($_POST);
        break;
    case 'nom_tipo_tabla':
        $content = (new ActividadTipoGetNomTipoTabla())->execute($_POST);
        break;
    case 'lugar':
        $content = (new ActividadTipoGetLugar())->execute($_POST);
        break;
    case 'id_tarifa':
        $content = (new ActividadTipoGetIdTarifa())->execute($_POST);
        break;
    case 'dl_org':
        $content = (new ActividadTipoGetDlOrg())->execute($_POST);
        break;
    case 'filtro_lugar':
        $content = (new ActividadTipoGetFiltroLugar())->execute($_POST);
        break;
    default:
        ContestarJson::enviar(sprintf(_('opción no definida: salida=%s'), $Qsalida));
        exit;
}

ContestarJson::enviar('', ['content' => (string)$content]);
