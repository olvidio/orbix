<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend que devuelve el payload necesario (datos de desplegable,
 * tabla HTML o valor escalar) segun el parametro POST `salida`. Usado por las
 * cascadas de filtros de actividades desde callers AJAX (templates Twig/phtml).
 *
 * Responde siempre JSON via src\shared\web\ContestarJson. Las salidas de tipo desplegable
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
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qsalida = (string)\src\shared\domain\helpers\FilterPostGet::post('salida');

// Salidas que devuelven el payload estructurado de un desplegable
// (id, opciones, selected, blanco, val_blanco, action). El frontend construye
// el `<select>`. El payload se envia directamente bajo `data`.
switch ($Qsalida) {
    case 'asistentes':
        /** @var ActividadTipoGetAsistentes $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetAsistentes::class);
        ContestarJson::enviar('', $useCase->execute($_POST));
        exit;
    case 'actividad':
        /** @var ActividadTipoGetActividad $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetActividad::class);
        ContestarJson::enviar('', $useCase->execute($_POST));
        exit;
    case 'nom_tipo':
        /** @var ActividadTipoGetNomTipo $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetNomTipo::class);
        ContestarJson::enviar('', $useCase->execute($_POST));
        exit;
    case 'lugar':
        /** @var ActividadTipoGetLugar $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetLugar::class);
        ContestarJson::enviar('', $useCase->execute($_POST));
        exit;
    case 'dl_org':
        /** @var ActividadTipoGetDlOrg $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetDlOrg::class);
        ContestarJson::enviar('', $useCase->execute($_POST));
        exit;
    case 'filtro_lugar':
        /** @var ActividadTipoGetFiltroLugar $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetFiltroLugar::class);
        ContestarJson::enviar('', $useCase->execute($_POST));
        exit;
}

// Salidas que devuelven HTML/valor escalar (bajo `content`).
switch ($Qsalida) {
    case 'nom_tipo_tabla':
        /** @var ActividadTipoGetNomTipoTabla $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetNomTipoTabla::class);
        $content = $useCase->execute($_POST);
        break;
    case 'id_tarifa':
        /** @var ActividadTipoGetIdTarifa $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetIdTarifa::class);
        $content = $useCase->execute($_POST);
        break;
    case 'nivel_stgr_defecto':
        /** @var ActividadTipoGetNivelStgrDefecto $useCase */
        $useCase = DependencyResolver::get(ActividadTipoGetNivelStgrDefecto::class);
        $content = $useCase->execute($_POST);
        break;
    default:
        ContestarJson::enviar(sprintf(_('opción no definida: salida=%s'), $Qsalida));
        exit;
}

ContestarJson::enviar('', ['content' => (string)$content]);
