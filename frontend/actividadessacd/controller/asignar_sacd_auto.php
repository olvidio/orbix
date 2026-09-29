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
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;
use frontend\actividadessacd\helpers\ActividadessacdSession;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$formData = PostRequest::getDataFromUrl('/src/actividadessacd/sacd_asignar_auto_form_data', []);
$inicurs_des_iso = \frontend\shared\helpers\PayloadCoercion::string($formData['f_ini_iso'] ?? '');
$ctx_asignar_auto = \frontend\shared\helpers\PayloadCoercion::string($formData['ctx_asignar_auto'] ?? '');

$idioma = ActividadessacdSession::sessionIdioma();
$a_idioma = explode('.', $idioma);
$code_lng = $a_idioma[0];
$sep = '/';
$fmtLocal = ($code_lng === 'en_US')
    ? 'n' . $sep . 'j' . $sep . 'Y'
    : 'j' . $sep . 'n' . $sep . 'Y';
$inicurs_des = $inicurs_des_iso;
if ($inicurs_des_iso !== '') {
    $oF = \DateTime::createFromFormat('Y-m-d', $inicurs_des_iso);
    if ($oF instanceof \DateTime) {
        $inicurs_des = $oF->format($fmtLocal);
    }
}

$buildHashedUrl = static function (string $url, string $campos): string {
    $oHash = new HashF();
    $oHash->setUrl($url);
    $oHash->setCamposForm($campos);
    return $url . $oHash->linkSinVal();
};

$url_asignar_auto = $buildHashedUrl(
    AppUrlConfig::srcBrowserUrl('/src/actividadessacd/sacd_asignar_auto'),
    'ctx_asignar_auto'
);

$a_campos = [
    'oPosicion' => $oPosicion,
    'inicurs_des' => $inicurs_des,
    'inicurs_des_iso' => $inicurs_des_iso,
    'ctx_asignar_auto' => $ctx_asignar_auto,
    'url_asignar_auto' => $url_asignar_auto,
];

$oView = new ViewNewPhtml('frontend\\actividadessacd\\controller');
$oView->renderizar('asignar_sacd_auto.phtml', $a_campos);
