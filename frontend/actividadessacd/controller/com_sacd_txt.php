<?php

/**
 * Pantalla de edicion de textos de comunicacion a los sacd.
 *
 * Renderiza dos desplegables (`clave`, `idioma`) + un `<textarea>` con
 * el texto inicial correspondiente a `com_sacd/es`. Las operaciones de
 * lectura y guardado se hacen via AJAX contra los endpoints
 * `/src/actividadessacd/texto_comunicacion_data` y
 * `/src/actividadessacd/texto_comunicacion_guardar` definidos en
 * `src/actividadessacd/config/routes.php`.
 *
 * Migrada desde `apps/actividadessacd/controller/com_sacd_txt.php` +
 * `apps/actividadessacd/controller/com_sacd_txt_ajax.php` (dispatcher
 * legacy con ramas `get_texto` y `update`) siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$a_Claves = [
    'com_sacd' => _("comunicación a los sacerdotes"),
    't_propio' => _("titulo: propio"),
    't_f_ini' => _("titulo: f_ini"),
    't_f_fin' => _("titulo: f_fin"),
    't_nombre_ubi' => _("titulo: nombre ubi"),
    't_sfsv' => _("titulo: sfsv"),
    't_actividad' => _("titulo: actividad"),
    't_asistentes' => _("titulo: asistentes"),
    't_encargado' => _("titulo: encargado"),
    't_observ' => _("titulo: observaciones"),
    't_nom_tipo' => _("titulo: nom_tipo"),
];
$oDesplClaves = new Desplegable();
$oDesplClaves->setNombre('clave');
$oDesplClaves->setOpciones($a_Claves);
$oDesplClaves->setOpcion_sel('com_sacd');
$oDesplClaves->setAction('fnjs_get_texto()');

$locData = PostRequest::getDataFromUrl('/src/actividadessacd/locales_desplegable_data', []);
$a_locales = (array)($locData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, 'es', true);
$oDesplIdiomas->setAction('fnjs_get_texto()');

// texto inicial (com_sacd / es).
$initial = PostRequest::getDataFromUrl('/src/actividadessacd/texto_comunicacion_data', [
    'clave' => 'com_sacd',
    'idioma' => 'es',
]);
$comunicacion = (string)($initial['texto'] ?? '');

$api = AppUrlConfig::getApiBaseUrl();
$buildHashedUrl = static function (string $url, string $campos): string {
    $oHash = new HashFront();
    $oHash->setUrl($url);
    $oHash->setCamposForm($campos);
    return $url . $oHash->linkSinVal();
};

$url_data = $buildHashedUrl(
    $api . '/src/actividadessacd/texto_comunicacion_data',
    'clave!idioma'
);
$url_guardar = $buildHashedUrl(
    $api . '/src/actividadessacd/texto_comunicacion_guardar',
    'clave!idioma!texto'
);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oDesplClaves' => $oDesplClaves,
    'oDesplIdiomas' => $oDesplIdiomas,
    'comunicacion' => $comunicacion,
    'url_data' => $url_data,
    'url_guardar' => $url_guardar,
];

$oView = new ViewNewPhtml('frontend\\actividadessacd\\controller');
$oView->renderizar('com_sacd_txt.phtml', $a_campos);
