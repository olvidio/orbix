<?php

use frontend\actividades\helpers\ActividadesPermSupport;
use frontend\casas\helpers\CasasPayload;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

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

use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ListNavSupport::buildReturnParametrosFromPost(),
);

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qsfsv = (string)filter_input(INPUT_POST, 'sfsv');

$oForm = new CasasQue();
$oForm->setTitulo(\src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb((string)_('búsqueda de casas cuyo resumen económico interesa')));
$filtro = ['active' => true];
$miSfsv = OrbixRuntime::miSfsv();
$miRolePau = OrbixRuntime::miRolePau();
// PauType::PAU_CDC (literal 'cdc').
if ($miRolePau === 'cdc') {
    $id_pau = CasasPayload::miUsuarioCsvIdPau();
    $filtro['id_ubi_in'] = array_values(array_filter(array_map('intval', explode(',', $id_pau)), static fn ($v) => $v > 0));
    $oForm->setCasas('casa');
} elseif (ActividadesPermSupport::havePermOficina('des') || ActividadesPermSupport::havePermOficina('vcsd')) {
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

$oSelects = $oForm->getSelects();
$oSelects->setAction('');

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
$oFormP->setTitulo(\src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb((string)_('periodo para el resumen económico')));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel(CasasPayload::periodoYearSel((int)date('Y')));
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
$oView->renderizar('casas_resumen.phtml', $a_campos);
