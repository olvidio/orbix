<?php

use frontend\notas\helpers\ActaImprimirPayload;
use frontend\notas\helpers\ActaImprimirPostInput;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\session\SessionConfig;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

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

$acta = ActaImprimirPostInput::actaFromPost();
$cara = ActaImprimirPostInput::caraFromPost();

$navState = array_merge(
    ['acta' => $acta, 'cara' => $cara],
    ListNavSupport::buildSelectionStatePatchFromPost(),
);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['acta' => $acta],
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    array_merge(
        ListNavSupport::buildActaImprimirParentReturnParametros(),
        ListNavSupport::buildSelectionStatePatchFromPost(),
    ),
);

$replace = OrbixRuntime::latinHtmlEntityReplaceMap();
$region_latin = SessionConfig::getNomRegionLatin();
$nombre_prelatura = strtr('PRAELATURA SANCTAE CRUCIS ET OPERIS DEI', $replace);
$reg_stgr = 'Stgr' . OrbixRuntime::miRegion();

$payload = PostRequest::getDataFromUrl('/src/notas/acta_imprimir_presentacion_data', [
    'acta' => $acta,
    'mode' => 'imprimir',
]);
$presentacion = ActaImprimirPayload::presentacionFromPayload($payload);
$errores = $presentacion['errores'];
$aPersonasNotas = $presentacion['aPersonasNotas'];
$num_alumnos = $presentacion['num_alumnos'];
$lin_max_cara_A = $presentacion['lin_max_cara_A'];
$lin_tribunal = $presentacion['lin_tribunal'];
$alum_cara_A = $presentacion['alum_cara_A'];
$alum_cara_B = $presentacion['alum_cara_B'];
$curso = $presentacion['curso'];
$any = $presentacion['any'];
$nombre_asignatura = $presentacion['nombre_asignatura'];
$libro = $presentacion['libro'];
$pagina = $presentacion['pagina'];
$linea = $presentacion['linea'];
$lugar = $presentacion['lugar'];
$lugar_fecha = $presentacion['lugar_fecha'];
$examinadores = $presentacion['examinadores'];
$acta = $presentacion['acta'] !== '' ? $presentacion['acta'] : $acta;

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
