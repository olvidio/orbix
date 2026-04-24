<?php
/**
 * Pantalla `casa_resumen`: filtro casa + periodo y carga AJAX del
 * resumen económico. Migrada desde `apps/casas/controller/casas_resumen.php`
 * (que además envía por `form.submit` en lugar de AJAX; en la nueva
 * versión unificamos el comportamiento de `casa_ec`).
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\domain\value_objects\PauType;
use web\CasasQue;
use web\Hash;
use web\PeriodoQue;

use function core\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qsfsv = (string)filter_input(INPUT_POST, 'sfsv');

$oMiUsuario = ConfigGlobal::MiUsuario();
$miSfsv = ConfigGlobal::mi_sfsv();
$miRolePau = ConfigGlobal::mi_role_pau();

$oForm = new CasasQue();
$oForm->setTitulo(strtoupper_dlb((string)_('búsqueda de casas cuyo resumen económico interesa')));
if ($miRolePau === PauType::PAU_CDC) {
    $id_pau = $oMiUsuario->getCsv_id_pau();
    $sDonde = str_replace(',', ' OR id_ubi=', $id_pau);
    $donde = "WHERE active='t' AND (id_ubi=$sDonde)";
    $oForm->setCasas('casa');
} elseif ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
    $donde = "WHERE active='t'";
} elseif ($miSfsv === 1) {
    $oForm->setCasas('sv');
    $donde = "WHERE active='t' AND sv='t'";
} elseif ($miSfsv === 2) {
    $oForm->setCasas('sf');
    $donde = "WHERE active='t' AND sf='t'";
} else {
    $donde = "WHERE active='t'";
}
$oForm->setPosiblesCasas($donde);
$oForm->setAction('');

$oSelects = $oForm->getDesplCasas();
$oSelects->setAction('');
$oSelects->setAccionConjunto('fnjs_mas_casas(event)');

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
$oFormP->setFormName('seleccion');
$oFormP->setTitulo(strtoupper_dlb((string)_('periodo para el resumen económico')));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel((int)date('Y'));
$oFormP->setAntes($oSelects->ListaSelects());
$oFormP->setBoton("<input type='button' name='buscar' value='" . _('buscar') . "' onclick='fnjs_ver();'>");

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_ajax = $web . '/frontend/casas/controller/casas_resumen_lista.php';

$oHash = new Hash();
$sCamposForm = 'cdc_sel!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!id_cdc!id_cdc_mas!id_cdc_num!periodo!sfsv!tipo!year!que';
$aCamposHidden = [
    'tipo' => $Qtipo,
    'sfsv' => $Qsfsv,
];
$oHash->setArrayCamposHidden($aCamposHidden);
$oHash->setCamposForm($sCamposForm);
$oHash->setCamposNo('id_cdc');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
    'oSelects' => $oSelects,
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('casa_resumen.phtml', $a_campos);
