<?php
/**
 * Pantalla auxiliar "Auto asignar sacd a actividades".
 *
 * Muestra un mensaje de confirmacion describiendo el criterio de
 * asignacion (sacd titular del centro encargado, actividades sr/sg
 * `status = ACTUAL` posteriores al inicio de curso des) y un boton
 * "continuar" que dispara un POST al endpoint
 * `/src/actividadessacd/sacd_asignar_auto`. El resultado (`asignadas`,
 * `sin_asignar`) se pinta en el propio div sin recargar la pagina.
 *
 * Migrada desde `apps/actividadessacd/controller/asignar_sacd_auto.php`
 * + `apps/actividadessacd/model/AsignarSacd.php` siguiendo `refactor.md`.
 * Sin `use src\...`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\actividadessacd\helpers\ActividadessacdSession;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$any_final_curs = ActividadessacdSession::anyFinalCurs();
$oF_inicurs_des = new \DateTime('@' . mktime(0, 0, 0, 9, 2, $any_final_curs));

$idioma = ActividadessacdSession::sessionIdioma();
$a_idioma = explode('.', $idioma);
$code_lng = $a_idioma[0];
$sep = '/';
$fmtLocal = ($code_lng === 'en_US')
    ? 'n' . $sep . 'j' . $sep . 'Y'
    : 'j' . $sep . 'n' . $sep . 'Y';
$inicurs_des = $oF_inicurs_des->format($fmtLocal);
$inicurs_des_iso = $oF_inicurs_des->format('Y-m-d');

$api = AppUrlConfig::getApiBaseUrl();
$buildHashedUrl = static function (string $url, string $campos): string {
    $oHash = new HashFront();
    $oHash->setUrl($url);
    $oHash->setCamposForm($campos);
    return $url . $oHash->linkSinVal();
};

$url_asignar_auto = $buildHashedUrl(
    AppUrlConfig::srcBrowserUrl('/src/actividadessacd/sacd_asignar_auto'),
    'f_ini_iso'
);

$a_campos = [
    'oPosicion' => $oPosicion,
    'inicurs_des' => $inicurs_des,
    'inicurs_des_iso' => $inicurs_des_iso,
    'url_asignar_auto' => $url_asignar_auto,
];

$oView = new ViewNewPhtml('frontend\\actividadessacd\\controller');
$oView->renderizar('asignar_sacd_auto.phtml', $a_campos);
