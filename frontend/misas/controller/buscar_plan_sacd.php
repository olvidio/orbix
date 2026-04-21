<?php

use function core\strtoupper_dlb;

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use src\shared\domain\value_objects\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\PeriodoQue;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/misas/buscar_plan_sacd_data');

$a_sacd = $data['sacd_opciones'] ?? [];
$sacd_selected = (string)($data['sacd_selected'] ?? '');

$aOpciones = [
    'esta_semana' => _('esta semana'),
    'este_mes' => _('este mes'),
    'proxima_semana' => _('próxima semana de lunes a domingo'),
    'proximo_mes' => _('próximo mes natural'),
    'separador' => '---------',
    'otro' => _('otro'),
];

$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(strtoupper_dlb(_('seleccionar un periodo')));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel('esta_semana');
$oFormP->setisDesplAnysVisible(false);

$ohoy = new DateTimeLocal(date('Y-m-d'));
$shoy = $ohoy->format('d/m/Y');
$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$periodo_td_html = $oFormP->getTd();

$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(false);
$oDesplSacd->setAction('fnjs_ver_plan_sacd()');
if ($sacd_selected !== '') {
    $oDesplSacd->setOpcion_sel($sacd_selected);
}

$url_ver_plan_sacd = 'frontend/misas/controller/ver_plan_sacd.php';
$oHashPlanSacd = new Hash();
$oHashPlanSacd->setUrl($url_ver_plan_sacd);
$oHashPlanSacd->setCamposForm('id_sacd!periodo!empiezamin!empiezamax');
$h_plan_sacd = $oHashPlanSacd->linkSinVal();

$a_campos = [
    'oDesplSacd' => $oDesplSacd,
    'periodo_td_html' => $periodo_td_html,
    'url_ver_plan_sacd' => $url_ver_plan_sacd,
    'h_plan_sacd' => $h_plan_sacd,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('buscar_plan_sacd.phtml', $a_campos);
