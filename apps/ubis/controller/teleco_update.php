<?php

use core\ConfigGlobal;
use usuarios\model\entity\Usuario;
use function core\is_true;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miSfsv = ConfigGlobal::mi_sfsv();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qcampos_chk = (string)filter_input(INPUT_POST, 'campos_chk');

switch ($Qobj_pau) {
    case 'CentroDl':
        $obj = 'ubis\\model\\entity\\TelecoCtrDl';
        break;
    case 'CentroEx':
        $obj = 'ubis\\model\\entity\\TelecoCtrEx';
        break;
    case 'CasaDl':
        $obj = 'ubis\\model\\entity\\TelecoCdcDl';
        break;
    case 'CasaEx':
        $obj = 'ubis\\model\\entity\\TelecoCdcEx';
        break;
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $s_pkey = explode('#', $a_sel[0]);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $s_pkey = str_replace("'", '"', $s_pkey[0]);
    $a_pkey = unserialize(core\urlsafe_b64decode($s_pkey), ['allowed_classes' => false]);
} else {
    $s_pkey = (string)filter_input(INPUT_POST, 's_pkey');
    $a_pkey = unserialize(core\urlsafe_b64decode($s_pkey), ['allowed_classes' => false]);

}

switch ($Qmod) {
    case 'eliminar_teleco':
        $oUbi = new $obj($a_pkey);
        if ($oUbi->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oUbi->getErrorTxt();
        }
//		echo $oPosicion->go_atras(1);
        die();
        break;
    case 'teleco':
        if (empty($a_pkey)) {
            // es nuevo
            $oUbi = new $obj();
            $oUbi->setId_ubi($Qid_ubi);
        } else {
            $oUbi = new $obj($a_pkey);
        }
        break;
}

$campos_chk = empty($Qcampos_chk) ? array() : explode(',', $Qcampos_chk);
$oUbi->DBCarregar();
$oDbl = $oUbi->getoDbl();
$cDatosCampo = $oUbi->getDatosCampos();
foreach ($cDatosCampo as $oDatosCampo) {
    $camp = $oDatosCampo->getNom_camp();
    $valor = empty($_POST[$camp]) ? '' : $_POST[$camp];
    if ($oDatosCampo->datos_campo($oDbl, 'tipo') == "bool") { //si es un campo boolean, cambio los valores on, off... por true, false...
        if ($valor == "on") {
            $valor = 't';
            $a_values_o[$camp] = $valor;
        } else {
            // compruebo que esté en la lista de campos enviados
            if (in_array($camp, $campos_chk)) {
                $valor = 'f';
                $a_values_o[$camp] = $valor;
            }
        }
    } else {
        if (!isset($_POST[$camp])) continue;
        //pongo el valor nulo, sobretodo para las fechas.
        if (!is_array($_POST[$camp]) && (empty($_POST[$camp]) || trim($_POST[$camp]) == "")) {
            //si es un campo not null (y es null), pongo el valor por defecto
            if (is_true($oDatosCampo->datos_campo($oDbl, 'nulo'))) {
                $valor_predeterminado = $oDatosCampo->datos_campo($oDbl, 'valor');
                $a_values_o[$camp] = $valor_predeterminado;
            } else {
                $a_values_o[$camp] = NULL;
            }
        } else {
            $a_values_o[$camp] = $valor;
        }
    }
}
$oUbi->setAllAtributes($a_values_o);
$oUbi->DBGuardar();