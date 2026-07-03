<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Pantalla del formulario para listados particulares de SR.
 *
 * Los valores por defecto (status/periodo/tipo_activ/ubis) provienen de la
 * preferencia del usuario, que se consulta via PostRequest a
 * `/src/actividades/lista_sr_csv_que_datos`.
 *
 * Migrado desde frontend/actividades/controller/lista_sr_csv_que.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\CasasQue;
use frontend\shared\web\PeriodoQue;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

$aOpciones = [
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'curso_ca' => _("curso ca"),
    'curso_crt' => _("curso crt"),
    'separador1' => '---------',
    'otro' => _("otro"),
];

$data = PostRequest::getDataFromUrl('/src/actividades/lista_sr_csv_que_datos', []);
if (empty($Qperiodo)) {
    $Qperiodo = PayloadCoercion::string($data['periodo'] ?? 'curso_ca');
}
$sel_ubis = PayloadCoercion::string($data['sel_ubis'] ?? '');
$chk_status_1 = PayloadCoercion::string($data['chk_status_1'] ?? '');
$chk_status_2 = PayloadCoercion::string($data['chk_status_2'] ?? '');
$chk_activ_crt = PayloadCoercion::string($data['chk_activ_crt'] ?? '');
$chk_activ_cv = PayloadCoercion::string($data['chk_activ_cv'] ?? '');

$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setAntes('Periodo');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oForm = new CasasQue();
$oForm->setTitulo(FuncTablasSupport::strtoupperDlb(_("ocupación de casas compartidas")));
$oForm->setCasas('casa');
$oForm->setFiltroCasas(['active' => true]);
$oForm->setSeleccionados($sel_ubis);

$oHash = new HashFront();
$oHash->setCamposForm('empiezamin!empiezamax!c_activ!id_cdc_mas!id_cdc_num!periodo!status!year');
$oHash->setcamposNo('que!id_cdc!cdc_sel');
$a_camposHidden = [
    'que' => '',
];
$oHash->setArraycamposHidden($a_camposHidden);

$titulo = _("selección de actividades de san rafael");
$fullUrl = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/lista_sr_csv.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'titulo' => $titulo,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
    'chk_status_1' => $chk_status_1,
    'chk_status_2' => $chk_status_2,
    'chk_activ_crt' => $chk_activ_crt,
    'chk_activ_cv' => $chk_activ_cv,
    'fullUrl' => $fullUrl,
];

$oView = new ViewNewTwig('frontend/actividades/controller');
$oView->renderizar('lista_sr_csv_que.html.twig', $a_campos);
