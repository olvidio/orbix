<?php
/**
 * Pantalla `casa_resumen`: filtro casa + periodo y carga AJAX del
 * resumen económico. Migrada desde `apps/casas/controller/casas_resumen.php`
 * (que además envía por `form.submit` en lugar de AJAX; en la nueva
 * versión unificamos el comportamiento de `casa_ec`).
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\CasasQue;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;

use function frontend\shared\helpers\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qsfsv = (string)filter_input(INPUT_POST, 'sfsv');

$oMiUsuario = $_SESSION['session_auth']['MiUsuario'];
$miSfsv = OrbixRuntime::miSfsv();
$miRolePau = OrbixRuntime::miRolePau();

$oForm = new CasasQue();
$oForm->setTitulo(strtoupper_dlb((string)_('búsqueda de casas cuyo resumen económico interesa')));
$filtro = ['active' => true];
// PauType::PAU_CDC (literal 'cdc').
if ($miRolePau === 'cdc') {
    $id_pau = $oMiUsuario->getCsv_id_pau();
    $filtro['id_ubi_in'] = array_values(array_filter(array_map('intval', explode(',', (string)$id_pau)), static fn ($v) => $v > 0));
    $oForm->setCasas('casa');
} elseif ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
} elseif ($miSfsv === 1) {
    $oForm->setCasas('sv');
    $filtro['sv'] = true;
} elseif ($miSfsv === 2) {
    $oForm->setCasas('sf');
    $filtro['sf'] = true;
}
$oForm->setFiltroCasas($filtro);
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

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/casas/controller/casas_resumen_lista.php';

$oHash = new HashFront();
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
