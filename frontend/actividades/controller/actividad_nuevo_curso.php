<?php
/**
 * Pantalla que crea las actividades para el nuevo curso, copiando las del
 * curso de referencia.
 *
 * La ejecución (borrado + creación + comprobación de solapes) se realiza en
 * `/src/actividades/actividad_nuevo_curso_ejecutar` (`ActividadNuevoCursoEjecutar`).
 * Este controlador solo monta el formulario; el navegador POSTea al backend.
 *
 * Migrado desde frontend/actividades/controller/actividad_nuevo_curso.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

$url_ejecutar = AppUrlConfig::getApiBaseUrl() . '/src/actividades/actividad_nuevo_curso_ejecutar';

$oHash = new HashFront();
$oHash->setUrl($url_ejecutar);
$oHash->setCamposForm('year_ref!year');
$oHash->setCamposNo('ver_lista');

$any0 = (int)date('Y');
$any1 = $any0 - 1;
$any2 = $any0 - 2;
$year1 = $any0 + 1;
$year2 = $any0 + 2;
$year3 = $any0 + 3;

$txt_borrar = sprintf(_("Este progama eliminará todas las actividades para el nuevo curso (%s) en estado proyecto"), $year1);
$txt_crear = sprintf(_("Este progama creará las actividades para el nuevo curso (%s) tomando como base las de este curso"), $year1);
$txt_estado = _("Las actividades nuevas creadas, quedarán en el estado: proyecto");
$txt_ctr = _("Se copiarán los centros encargados de las actividades");
$txt_fases = _("Se crean las fases de cada actividad");
$txt_lento = _("Según el número de actividades, puede tardar un rato (3 minutos). Tómate un café...");

$txt = "<h1>" . _("atención") . ":</h1>";
$txt .= "<p>$txt_borrar.";
$txt .= "<p>$txt_crear.";
$txt .= "<p>$txt_estado.";
if (AppInstalled::is('actividadescentro')) {
    $txt .= "<p>$txt_ctr.";
}
if (AppInstalled::is('procesos')) {
    $txt .= "<p>$txt_fases.";
}

$a_campos = [
    'oHash' => $oHash,
    'txt' => $txt,
    'txt_lento' => $txt_lento,
    'any0' => $any0,
    'any1' => $any1,
    'any2' => $any2,
    'year1' => $year1,
    'year2' => $year2,
    'year3' => $year3,
    'url_ejecutar' => $url_ejecutar,
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('actividad_nuevo_curso.phtml', $a_campos);
