<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Esta página sirve para las actas.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        24/10/03.
 *
 */

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $acta = urldecode(strtok($a_sel[0], "#"));
} else {
    $Qacta = (string)filter_input(INPUT_POST, 'acta');
    $acta = empty($Qacta) ? '' : urldecode($Qacta);
}

$Qcara = (string)filter_input(INPUT_POST, 'cara');
$cara = empty($Qcara) ? 'A' : $Qcara;

$replace = OrbixRuntime::latinHtmlEntityReplaceMap();
$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$nombre_prelatura = strtr('PRAELATURA SANCTAE CRUCIS ET OPERIS DEI', $replace);
$reg_stgr = 'Stgr' . OrbixRuntime::miRegion();

$d = PostRequest::getDataFromUrl('/src/notas/acta_imprimir_presentacion_data', [
    'acta' => $acta,
    'mode' => 'imprimir',
]);
$errores = (string)($d['errores'] ?? '');
$aPersonasNotas = [];
foreach ($d['aPersonasNotas_list'] ?? [] as $row) {
    $aPersonasNotas[$row['nom']] = $row['nota'];
}
$num_alumnos = (int)($d['num_alumnos'] ?? 0);
$lin_max_cara_A = (int)($d['lin_max_cara_A'] ?? 0);
$lin_tribunal = (int)($d['lin_tribunal'] ?? 0);
$alum_cara_A = (int)($d['alum_cara_A'] ?? 0);
$alum_cara_B = (int)($d['alum_cara_B'] ?? 0);
$curso = (string)($d['curso'] ?? '');
$any = (string)($d['any'] ?? '');
$nombre_asignatura = (string)($d['nombre_asignatura'] ?? '');
$libro = (string)($d['libro'] ?? '');
$pagina = (string)($d['pagina'] ?? '');
$linea = (string)($d['linea'] ?? '');
$lugar = (string)($d['lugar'] ?? '');
$lugar_fecha = (string)($d['lugar_fecha'] ?? '');
$examinadores = $d['examinadores'] ?? [];

$caraA = HashFront::link('frontend/notas/controller/acta_imprimir.php?' . http_build_query(array('cara' => 'A', 'acta' => $acta, 'refresh' => 1)));
$caraB = HashFront::link('frontend/notas/controller/acta_imprimir.php?' . http_build_query(array('cara' => 'B', 'acta' => $acta, 'refresh' => 1)));

$url_pdf = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/notas/controller/acta_2_mpdf.php';
$oHash = new HashFront();
$oHash->setUrl($url_pdf);
$oHash->setCamposForm('acta');
$go_pdf = $url_pdf . "?acta=$acta&" . $oHash->linkConVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'go_pdf' => $go_pdf,
    'cara' => $cara,
    'caraA' => $caraA,
    'caraB' => $caraB,
    'acta' => $acta,
    'errores' => $errores,
    'curso' => $curso,
    'any' => $any,
    'region_latin' => $region_latin,
    'nombre_prelatura' => $nombre_prelatura,
    'reg_stgr' => $reg_stgr,
    'nombre_asignatura' => $nombre_asignatura,
    'alum_cara_A' => $alum_cara_A,
    'alum_cara_B' => $alum_cara_B,
    'aPersonasNotas' => $aPersonasNotas,
    'num_alumnos' => $num_alumnos,
    'lin_max_cara_A' => $lin_max_cara_A,
    'lin_tribunal' => $lin_tribunal,
    'examinadores' => $examinadores,
    'lugar' => $lugar,
    'lugar_fecha' => $lugar_fecha,
    'libro' => $libro,
    'pagina' => $pagina,
    'linea' => $linea,
];

$oView = new ViewNewPhtml('frontend\notas\controller');
$oView->renderizar('acta_imprimir.phtml', $a_campos);