<?php
/**
 * Muestra un deplegable con las dl disponibles para comparar el número de plazas
 *
 * @param integer $id_tipo_activ
 * o bien
 * @param string $ssfsv
 * @param string $sasistentes
 * @param string $ssctividad
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ViewPhtml;
use web\Hash;
use src\ubis\application\services\DelegacionDropdown;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
    // mejor que novenga por menú. Así solo veo las de mi sección.
    //$Qssfsv = (string)  filter_input(INPUT_POST, 'ssfsv');
    $Qssfsv = '';
    if (empty($Qssfsv)) {
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        if ($mi_sfsv == 1) $Qssfsv = 'sv';
        if ($mi_sfsv == 2) $Qssfsv = 'sf';
    }
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $oTipoActiv = new web\TiposActividades();
    $oTipoActiv->setSfsvText($Qssfsv);
    $oTipoActiv->setAsistentesText($Qsasistentes);
    $oTipoActiv->setActividadText($Qsactividad);
    $Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
}

$desplDelegaciones = DelegacionDropdown::activasOrdenNombre('dl');
$desplDelegaciones->setAction("fnjs_comparativa()") ;

$mi_dele = ConfigGlobal::mi_delef();
$txt = sprintf(_("comparar %s con:"), $mi_dele);

$oHash = new Hash();
$oHash->setUrl(ConfigGlobal::getWeb() . '/apps/actividadplazas/controller/plazas_balance_dl.php');
$oHash->setCamposForm('dl!id_tipo_activ');
$h = $oHash->linkSinVal();

$a_campos = [
    'Qid_tipo_activ' => $Qid_tipo_activ,
    'h' => $h,
    'txt' => $txt,
    'desplDelegaciones' => $desplDelegaciones,
];

$oView = new ViewPhtml('actividadplazas\controller');
$oView->renderizar('plazas_balance_que.phtml', $a_campos);