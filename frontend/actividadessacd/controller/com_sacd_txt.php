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

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividadessacd\application\TextoComunicacionData;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

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

$LocaleRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$a_locales = $LocaleRepository->getArrayLocales();
$oDesplIdiomas = new Desplegable('idioma', $a_locales, 'es', true);
$oDesplIdiomas->setAction('fnjs_get_texto()');

// texto inicial (com_sacd / es).
$initial = TextoComunicacionData::execute(['clave' => 'com_sacd', 'idioma' => 'es']);
$comunicacion = $initial['texto'] ?? '';

$web = rtrim(ConfigGlobal::getWeb(), '/');
$buildHashedUrl = static function (string $url, string $campos): string {
    $oHash = new Hash();
    $oHash->setUrl($url);
    $oHash->setCamposForm($campos);
    return $url . $oHash->linkSinVal();
};

$url_data = $buildHashedUrl(
    $web . '/src/actividadessacd/texto_comunicacion_data',
    'clave!idioma'
);
$url_guardar = $buildHashedUrl(
    $web . '/src/actividadessacd/texto_comunicacion_guardar',
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
