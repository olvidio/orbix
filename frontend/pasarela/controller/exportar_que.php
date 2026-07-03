<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\CasasQue;
use frontend\shared\web\PeriodoQue;
use frontend\shared\web\Posicion;
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
$web = AppUrlConfig::getPublicAppBaseUrl();

$restored = \frontend\shared\helpers\ListNavSupport::restoreSelectionFromStackPost();

/** @var string|list<string> $Qid_sel */
$Qid_sel = !\frontend\shared\helpers\ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : \frontend\shared\helpers\ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : \frontend\shared\helpers\ListNavSupport::scrollIdFromPost();
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros(\frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));



$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qcdc_sel = (integer)filter_input(INPUT_POST, 'cdc_sel');
$Qid_cdc_mas = (string)filter_input(INPUT_POST, 'id_cdc_mas');
$Qid_cdc_num = (string)filter_input(INPUT_POST, 'id_cdc_num');

$dataTipoHtml = PostRequest::getDataFromUrl('/src/pasarela/exportar_que_actividad_tipo_html', [
    'id_tipo_activ' => $Qid_tipo_activ,
    'sasistentes' => $Qsasistentes,
    'sactividad' => $Qsactividad,
    'snom_tipo' => $Qsnom_tipo,
]);
$html_actividad_tipo = \frontend\shared\helpers\PayloadCoercion::string($dataTipoHtml['html'] ?? '');

$aOpciones = [
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '---------',
    'otro' => _('otro'),
];
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);

$oForm = new CasasQue();
$oForm->setTitulo('');
$oForm->setCasas('casa');
$oForm->setFiltroCasas(['active' => true]);

$url_ajax = $web . '/frontend/pasarela/controller/exportar_select.php';

$oHash = new HashFront();
$oHash->setUrl($url_ajax);
$oHash->setCamposForm('cdc_sel!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!id_cdc!id_cdc_mas!id_cdc_num!id_tipo_activ!inom_tipo_val!isfsv_val!periodo!year');
$oHash->setCamposNo('cdc_sel!id_cdc!id_cdc_mas!id_cdc_num');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'html_actividad_tipo' => $html_actividad_tipo,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
    'url_ajax' => $url_ajax,
];

$oView = new ViewNewTwig('frontend\\pasarela\\controller');
$oView->renderizar('exportar_que.html.twig', $a_campos);
