<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\web\CasasQue;
use frontend\shared\web\PeriodoQue;
use frontend\shared\web\Posicion;
use src\actividades\application\ActividadTipo;
use src\shared\config\ConfigGlobal;

/**
 * Página para cambiar la fase a un grupo de actividades.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        2/8/2011.
 */

require_once 'frontend/shared/global_header_front.inc';
require_once __DIR__ . '/../../src/shared/global_object.inc';

$web = AppUrlConfig::getPublicAppBaseUrl();

$oPosicion->recordar();

if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

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

$isfsv = ConfigGlobal::mi_sfsv();
$permiso_des = FALSE;
if ($_SESSION['oPerm']->have_perm_oficina('vcsd')
    || $_SESSION['oPerm']->have_perm_oficina('des')
    || $_SESSION['oPerm']->have_perm_oficina('calendario')
) {
    $permiso_des = TRUE;
    $ssfsv = '';
} else {
    if ($isfsv === 1) {
        $ssfsv = 'sv';
    }
    if ($isfsv === 2) {
        $ssfsv = 'sf';
    }
}

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
$oActividadTipo->setSfsv($ssfsv);
$oActividadTipo->setAsistentes($Qsasistentes);
$oActividadTipo->setActividad($Qsactividad);
$oActividadTipo->setNom_tipo($Qsnom_tipo);
$oActividadTipo->setEvitarProcesos(TRUE);

$perm_jefe = FALSE;
if ($_SESSION['oConfig']->is_jefeCalendario()
    || (($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) && ConfigGlobal::mi_sfsv() === 1)
    || ($_SESSION['oPerm']->have_perm_oficina('admin_sf') && ConfigGlobal::mi_sfsv() === 2)
) {
    $perm_jefe = TRUE;
}
$oActividadTipo->setPerm_jefe($perm_jefe);
$oActividadTipo->setSfsvAll(TRUE);

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
    'oActividadTipo' => $oActividadTipo,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
    'url_ajax' => $url_ajax,
];

$oView = new ViewNewTwig('frontend\\pasarela\\controller');
$oView->renderizar('exportar_que.html.twig', $a_campos);
