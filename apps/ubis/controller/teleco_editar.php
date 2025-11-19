<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\ubis\application\repositories\DescTelecoRepository;
use src\ubis\application\repositories\TipoTelecoRepository;
use web\Desplegable;
use web\Hash;

/**
 * Es el frame inferior. Muestra la ficha de los ubis
 *
 * Se incluye la página ficha.php que contiene la función ficha.
 * Esta página sirve para definir los parámetros que se le pasan a la función ficha.
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $s_pkey = explode('#', $a_sel[0]);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $s_pkey = str_replace("'", '"', $s_pkey[0]);
    $a_pkey = json_decode(core\urlsafe_b64decode($s_pkey));
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else { // si es nuevo
    $s_pkey = '';
}

switch ($Qobj_pau) {
    case 'Centro': // tipo dl pero no de la mia
        $repoName = 'src\\ubis\\application\\repositories\\TelecoCtrRepository';
        break;
    case 'CentroDl':
        $repoName = 'src\\ubis\\application\\repositories\\TelecoCtrDlRepository';
        break;
    case 'CentroEx':
        $repoName = 'src\\ubis\\application\\repositories\\TelecoCtrExRepository';
        break;
    case 'Casa': // tipo dl pero no de la mia
        $repoName = 'src\\ubis\\application\\repositories\\TelecoCdcRepository';
        break;
    case 'CasaDl':
        $repoName = 'src\\ubis\\application\\repositories\\TelecoCdcDlRepository';
        break;
    case 'CasaEx':
        $repoName = 'src\\ubis\\application\\repositories\\TelecoCdcExRepository';
        break;
}

if ($Qmod === 'nuevo') {
    $desc_teleco = '';
    $id_tipo_teleco = '';
    $num_teleco = '';
    $observ = '';
} else {
    $repo = new $repoName();
    $TelecoUbi = $repo->findById($a_pkey);

    $desc_teleco = $TelecoUbi->getDesc_teleco();
    $id_tipo_teleco = $TelecoUbi->getId_tipo_teleco();
    $num_teleco = $TelecoUbi->getNum_teleco();
    $observ = $TelecoUbi->getObserv();
}

//----------------------------------Permisos según el usuario
$miSfsv = ConfigGlobal::mi_sfsv();

$botones = 0;
/*
1: guardar cambios
2: eliminar
3: eliminar
4: quitar direccion
*/
switch ($Qobj_pau) {
    case 'CentroDl':
    case 'CasaDl':
        $objfull = 'ubis\\model\\entity\\' . $Qobj_pau;
        $oUbi = new $objfull($Qid_ubi);
        $dl = $oUbi->getDl();
        if ($dl == ConfigGlobal::mi_delef()) {
            // ----- sv sólo a scl -----------------
            if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
                $botones = "1,3";
            }
        }
        break;
    case 'CentroEx':
    case 'CasaEx':
        // ----- sv sólo a scl -----------------
        if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
            $botones = "1,3";
        }
        break;
}

$campos_chk = '';

$TipoTelecoRepository = new TipoTelecoRepository();
$aOpciones = $TipoTelecoRepository->getArrayTiposTelecoUbi();
$oDesplegableTiposTeleco = new Desplegable();
$oDesplegableTiposTeleco->setNombre('id_tipo_teleco');
$oDesplegableTiposTeleco->setOpciones($aOpciones);
$oDesplegableTiposTeleco->setOpcion_sel($id_tipo_teleco);
$oDesplegableTiposTeleco->setAction('fnjs_actualizar_descripcion()');
$oDesplegableTiposTeleco->setBlanco(true);

$oDescTeleco = new DescTelecoRepository();
$aOpciones = [];
if (!empty($id_tipo_teleco)) {
    $aOpciones = $oDescTeleco->getArrayDescTelecoUbis($id_tipo_teleco);
}
$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($aOpciones);
$oDesplegableDescTeleco->setNombre('desc_teleco');
$oDesplegableDescTeleco->setOpcion_sel($desc_teleco);
$oDesplegableDescTeleco->setBlanco(true);

$url_actualizar = ConfigGlobal::getWeb() . '/apps/ubis/controller/teleco_ajax.php';
$oHash1 = new Hash();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('id_tipo_teleco');
$h_actualizar = $oHash1->linkSinVal();

$oHash = new Hash();
$oHash->setCamposForm('mod!id_tipo_teleco!desc_teleco!num_teleco!observ');
$oHash->setcamposNo('mod!' . $campos_chk);
$a_camposHidden = array(
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
    's_pkey' => $s_pkey,
);
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = ['obj' => $repoName,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oDesplegableTiposTeleco' => $oDesplegableTiposTeleco,
    'oDesplegableDescTeleco' => $oDesplegableDescTeleco,
    'num_teleco' => $num_teleco,
    'observ' => $observ,
    'botones' => $botones,
    'url_actualizar' => $url_actualizar,
    'h_actualizar' => $h_actualizar,
];

$oView = new ViewPhtml('ubis\controller');
$oView->renderizar('teleco_form.phtml', $a_campos);