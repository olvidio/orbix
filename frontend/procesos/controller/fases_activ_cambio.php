<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * Página para cambiar la fase a un grupo de actividades.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        2/8/2011.
 */

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$apiBase = AppUrlConfig::getApiBaseUrl();

$restored = ListNavSupport::restoreSelectionFromStackPost();

/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();
$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    ListNavSupport::buildReturnParametrosFromPost(),
    $Qid_sel,
    $Qscroll_id,
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::buildSelectionStatePatchFromPost(),
);


$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$Qid_fase_nueva = (string)filter_input(INPUT_POST, 'id_fase_nueva');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qyear = (string)filter_input(INPUT_POST, 'year');

$Qsactividad2 = (string)filter_input(INPUT_POST, 'sactividad2');

$extendida = false;
if (!empty($Qsactividad2)) {
    $extendida = true;
}

$dataTipo = PostRequest::getDataFromUrl(AppUrlConfig::srcBrowserUrl('/src/procesos/fases_activ_cambio_tipo_html'), [
    'id_tipo_activ' => $Qid_tipo_activ,
    'sasistentes' => $Qsasistentes,
    'sactividad' => $Qsactividad,
    'sactividad2' => $Qsactividad2,
]);
$tipo_actividad_html = \frontend\shared\helpers\PayloadCoercion::string($dataTipo['tipo_actividad_html'] ?? '');

$aOpciones = [
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro"),
];
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);


$url_lista = 'frontend/procesos/controller/fases_activ_cambio_lista.php';
$url_update = AppUrlConfig::srcBrowserUrl('/src/procesos/fases_activ_cambio_update');
$url_get = AppUrlConfig::srcBrowserUrl('/src/procesos/fases_activ_cambio_get');

$oHashLista = new HashFront();
$oHashLista->setUrl($url_lista);
$oHashLista->setCamposForm('dl_propia!id_tipo_activ!id_fase_nueva!periodo!year!empiezamax!empiezamin!accion');
$h_lista = $oHashLista->linkSinValParams();

$oHashAct = new HashFront();
$oHashAct->setUrl($url_get);
$oHashAct->setCamposForm('dl_propia!id_tipo_activ!id_fase_sel');
$h_actualizar = $oHashAct->linkSinValParams();

$url_tipo = AppUrlConfig::srcBrowserUrl('/src/actividades/actividad_tipo_get');
$oHash1 = new HashFront();
$oHash1->setUrl($url_tipo);
$oHash1->setCamposForm('extendida!modo!salida!entrada');
$h_tipo = $oHash1->linkSinVal();

$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");

if ($Qdl_propia === 'f') {
    $chk_propia = '';
    $chk_no_propia = 'checked';
} else {
    $chk_propia = 'checked';
    $chk_no_propia = '';
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_lista' => $h_lista,
    'h_actualizar' => $h_actualizar,
    'h_tipo' => $h_tipo,
    'tipo_actividad_html' => $tipo_actividad_html,
    'extendida' => $extendida,
    'oFormP' => $oFormP,
    'url_lista' => $url_lista,
    'url_update' => $url_update,
    'url_get' => $url_get,
    'url_tipo' => $url_tipo,
    'txt_eliminar' => $txt_eliminar,
    'chk_propia' => $chk_propia,
    'chk_no_propia' => $chk_no_propia,
    'id_fase_nueva' => $Qid_fase_nueva,
];

$oView = new ViewNewTwig('frontend/procesos/controller');
$oView->renderizar('fases_activ_cambio.html.twig', $a_campos);
