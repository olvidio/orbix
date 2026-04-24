<?php
/**
 * Pantalla que crea las actividades para el nuevo curso, copiando las del
 * curso de referencia.
 *
 * La ejecucion (borrado + creacion + comprobacion de solapes) se realiza en
 * `src\actividades\application\ActividadNuevoCursoEjecutar` via PostRequest.
 * Este controlador solo gestiona el formulario y muestra el resultado HTML.
 *
 * Migrado desde frontend/actividades/controller/actividad_nuevo_curso.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$Qok = (int)filter_input(INPUT_POST, 'ok');
$Qver_lista = (string)filter_input(INPUT_POST, 'ver_lista');

if ($Qok === 1) {
    $Qyear_ref = (int)filter_input(INPUT_POST, 'year_ref');
    $Qyear = (int)filter_input(INPUT_POST, 'year');

    $data = PostRequest::getDataFromUrl('/src/actividades/actividad_nuevo_curso_ejecutar', [
        'year_ref' => $Qyear_ref,
        'year' => $Qyear,
        'ver_lista' => $Qver_lista,
    ]);

    echo (string)($data['html'] ?? '');
    return;
}

$oHash = new Hash();
$a_camposHidden = [
    'ok' => 1,
];
$oHash->setCamposForm('year_ref!year');
$oHash->setCamposNo('ver_lista');
$oHash->setArraycamposHidden($a_camposHidden);

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
if (ConfigGlobal::is_app_installed('actividadescentro')) {
    $txt .= "<p>$txt_ctr.";
}
if (ConfigGlobal::is_app_installed('procesos')) {
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
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('actividad_nuevo_curso.phtml', $a_campos);
