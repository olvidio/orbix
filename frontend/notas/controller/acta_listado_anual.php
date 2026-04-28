<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Periodo;
use frontend\shared\web\PeriodoQue;
use src\shared\domain\value_objects\DateTimeLocal;

require_once 'frontend/shared/global_header_front.inc';

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

if (empty($Qperiodo)) {
    $Qperiodo = 'curso_ca';
}

$oPeriodo = Periodo::conCalendarioDesdeBackend();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);
$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

$data = PostRequest::getDataFromUrl('/src/notas/acta_listado_anual_data', [
    'inicioIso' => $inicioIso,
    'finIso' => $finIso,
]);

$aActas = $data['aActas'] ?? [];

$aOpciones = [
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'curso_ca' => _("curso ca"),
    'separador1' => '---------',
    'otro' => _("otro"),
];
$boton = "<input type='button' value='" . _("buscar") . "' onclick='fnjs_buscar()' >";
$oFormP = new PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(src\shared\domain\helpers\strtoupper_dlb(_("periodo de selección")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setBoton($boton);

$oHashPeriodo = new HashFront();
$oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHashPeriodo->setCamposNo('!refresh');
$oHashPeriodo->setArraycamposHidden([]);

$QinicioLocal = (new DateTimeLocal($inicioIso))->getFromLocal();
$QfinLocal = (new DateTimeLocal($finIso))->getFromLocal();
$titulo = sprintf(_("Lista de actas en el periodo: %s - %s."), $QinicioLocal, $QfinLocal);

$a_campos = [
    'aActas' => $aActas,
    'titulo' => $titulo,
    'oFormP' => $oFormP,
    'oHashPeriodo' => $oHashPeriodo,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('acta_listado_anual.phtml', $a_campos);
