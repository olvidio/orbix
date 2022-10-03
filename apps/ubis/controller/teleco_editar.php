<?php

use usuarios\model\entity as usuarios;
use web\Desplegable;

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

$Qobj_pau = (string)\filter_input(INPUT_POST, 'obj_pau');
$Qmod = (string)\filter_input(INPUT_POST, 'mod');
$Qid_ubi = (integer)\filter_input(INPUT_POST, 'id_ubi');

$a_sel = (array)\filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $s_pkey = explode('#', $a_sel[0]);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $s_pkey = str_replace("'", '"', $s_pkey[0]);
    $a_pkey = unserialize(core\urlsafe_b64decode($s_pkey));
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)\filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else { // si es nuevo
    $s_pkey = '';
}

switch ($Qobj_pau) {
    case 'Centro': // tipo dl pero no de la mia
        $obj = 'ubis\\model\\entity\\TelecoCtr';
        break;
    case 'CentroDl':
        $obj = 'ubis\\model\\entity\\TelecoCtrDl';
        break;
    case 'CentroEx':
        $obj = 'ubis\\model\\entity\\TelecoCtrEx';
        break;
    case 'Casa': // tipo dl pero no de la mia
        $obj = 'ubis\\model\\entity\\TelecoCdc';
        break;
    case 'CasaDl':
        $obj = 'ubis\\model\\entity\\TelecoCdcDl';
        break;
    case 'CasaEx':
        $obj = 'ubis\\model\\entity\\TelecoCdcEx';
        break;
}

if ($Qmod == 'nuevo') {
    $desc_teleco = '';
    $tipo_teleco = '';
    $num_teleco = '';
    $observ = '';
    $oUbi = new $obj();
    $cDatosCampo = $oUbi->getDatosCampos();
    $oDbl = $oUbi->getoDbl();
    foreach ($cDatosCampo as $oDatosCampo) {
        $camp = $oDatosCampo->getNom_camp();
        $valor_predeterminado = $oDatosCampo->datos_campo($oDbl, 'valor');
        $a_campos[$camp] = $valor_predeterminado;

    }
} else {
    $oUbi = new $obj($a_pkey);
    $desc_teleco = $oUbi->getDesc_teleco();
    $tipo_teleco = $oUbi->getTipo_teleco();
    $num_teleco = $oUbi->getNum_teleco();
    $observ = $oUbi->getObserv();
}

//----------------------------------Permisos según el usuario
$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv = core\ConfigGlobal::mi_sfsv();

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
        if ($dl == core\ConfigGlobal::mi_delef()) {
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

$oTiposTeleco = new ubis\model\entity\GestorTipoTeleco();
$oDesplegableTiposTeleco = $oTiposTeleco->getListaTiposTelecoUbi();
$oDesplegableTiposTeleco->setNombre('tipo_teleco');
$oDesplegableTiposTeleco->setOpcion_sel($tipo_teleco);
$oDesplegableTiposTeleco->setAction('fnjs_actualizar_descripcion()');
$oDesplegableTiposTeleco->setBlanco(true);

$oDescTeleco = new ubis\model\entity\GestorDescTeleco();
$aOpciones = $oDescTeleco->getListaDescTelecoUbis($tipo_teleco);
$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($aOpciones);
$oDesplegableDescTeleco->setNombre('desc_teleco');
$oDesplegableDescTeleco->setOpcion_sel($desc_teleco);
$oDesplegableDescTeleco->setBlanco(true);

$url_actualizar = core\ConfigGlobal::getWeb() . '/apps/ubis/controller/teleco_ajax.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url_actualizar);
$oHash1->setCamposForm('tipo_teleco');
$h_actualizar = $oHash1->linkSinVal();

$oHash = new web\Hash();
$oHash->setcamposForm('mod!tipo_teleco!desc_teleco!num_teleco!observ');
$oHash->setcamposNo('mod!' . $campos_chk);
$a_camposHidden = array(
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
    's_pkey' => $s_pkey,
);
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = ['obj' => $obj,
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

$oView = new core\View('ubis\controller');
echo $oView->render('teleco_form.phtml', $a_campos);