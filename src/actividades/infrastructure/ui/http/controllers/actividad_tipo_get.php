<?php
/**
 * Endpoint backend que devuelve un partial HTML (desplegable, tabla o id)
 * segun el parametro POST `salida`. Usado para cascadas de filtros de
 * actividades por callers AJAX (templates Twig/phtml).
 *
 * Cada accion del switch delega en su use case en src\actividades\application.
 */

use src\actividades\application\ActividadTipoGetActividad;
use src\actividades\application\ActividadTipoGetAsistentes;
use src\actividades\application\ActividadTipoGetDlOrg;
use src\actividades\application\ActividadTipoGetFiltroLugar;
use src\actividades\application\ActividadTipoGetIdTarifa;
use src\actividades\application\ActividadTipoGetLugar;
use src\actividades\application\ActividadTipoGetNomTipo;
use src\actividades\application\ActividadTipoGetNomTipoTabla;

header('Content-Type: text/plain; charset=UTF-8');

$Qsalida = (string)filter_input(INPUT_POST, 'salida');

switch ($Qsalida) {
    case 'asistentes':
        echo (new ActividadTipoGetAsistentes())->execute($_POST);
        break;
    case 'actividad':
        echo (new ActividadTipoGetActividad())->execute($_POST);
        break;
    case 'nom_tipo':
        echo (new ActividadTipoGetNomTipo())->execute($_POST);
        break;
    case 'nom_tipo_tabla':
        echo (new ActividadTipoGetNomTipoTabla())->execute($_POST);
        break;
    case 'lugar':
        echo (new ActividadTipoGetLugar())->execute($_POST);
        break;
    case 'id_tarifa':
        echo (new ActividadTipoGetIdTarifa())->execute($_POST);
        break;
    case 'dl_org':
        echo (new ActividadTipoGetDlOrg())->execute($_POST);
        break;
    case 'filtro_lugar':
        echo (new ActividadTipoGetFiltroLugar())->execute($_POST);
        break;
    default:
        http_response_code(400);
        echo sprintf(_("opción no definida: salida=%s"), $Qsalida);
}
