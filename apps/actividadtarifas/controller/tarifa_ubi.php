<?php
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$oForm = new web\CasasQue();
$miSfsv = core\ConfigGlobal::mi_sfsv();

// Sólo quiero ver las casas comunes.
//$donde="WHERE status='t' AND sf='t' AND sv='t'";
// o (ara) no:
if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
    $donde = "WHERE status='t'";
} else {
    if ($miSfsv == 1) {
        $oForm->setCasas('sv');
        $donde = "WHERE status='t' AND sv='t'";
    } elseif ($miSfsv == 2) {
        $oForm->setCasas('sf');
        $donde = "WHERE status='t' AND sf='t'";
    }
}
$oForm->setPosiblesCasas($donde);
$oForm->setAction('fnjs_ver()');

$oFormAny = new web\PeriodoQue();
$oFormAny->setAction('fnjs_ver()');

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_ajax.php');
$oHash->setCamposForm('que!id_ubi!year');
$h_ver = $oHash->linkSinVal();

$oHashNew = new web\Hash();
$oHashNew->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_ajax.php');
$oHashNew->setCamposForm('que!id_ubi!year');
$h_nuevo = $oHashNew->linkSinVal();

$oHashMod = new web\Hash();
$oHashMod->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_ajax.php');
$oHashMod->setCamposForm('que!id_item!letra');
$h_modificar = $oHashMod->linkSinVal();

$txt_eliminar = _("¿Está seguro que quiere eliminar esta tarifa?");
$a_campos = ['oPosicion' => $oPosicion,
    'h_ver' => $h_ver,
    'h_nuevo' => $h_nuevo,
    'h_modificar' => $h_modificar,
    'oForm' => $oForm,
    'oFormAny' => $oFormAny,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new core\View('actividadtarifas/controller');
$oView->renderizar('tarifa_ubi.phtml', $a_campos);