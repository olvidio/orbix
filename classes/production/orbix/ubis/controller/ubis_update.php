<?php

use core\ConfigGlobal;
use ubis\model\entity\CdcDlxDireccion;
use ubis\model\entity\CdcExxDireccion;
use ubis\model\entity\CtrDlxDireccion;
use ubis\model\entity\CtrExxDireccion;
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

$oMiUsuario = ConfigGlobal::MiUsuario();

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qcampos_chk = (string)filter_input(INPUT_POST, 'campos_chk');

$campos_chk = empty($Qcampos_chk) ? [] : explode('!', $Qcampos_chk);

switch ($Qque) {
    case 'eliminar_ubi':
        $obj = 'ubis\\model\\entity\\' . $Qobj_pau;
        $oUbi = new $obj($Qid_ubi);
        if ($oUbi->DBEliminar() === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $oUbi->getErrorTxt();
        }
        die();
        break;
    case 'ubi':
        $obj = 'ubis\\model\\entity\\' . $Qobj_pau;
        $oUbi = new $obj($Qid_ubi);
        guardarObjeto($oUbi, $campos_chk);
        break;
    case 'direccion':
        $Qidx = (string)filter_input(INPUT_POST, 'idx');
        $Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
        $Qpropietario = (string)filter_input(INPUT_POST, 'propietario');
        $Qprincipal = (string)filter_input(INPUT_POST, 'principal');

        if ($Qidx === 'nuevo') {
            $obj = 'ubis\\model\\entity\\' . $Qobj_dir;
            $oDireccion = new $obj();
            guardarObjeto($oDireccion, $campos_chk);

            $oDireccion->DBCarregar();
            $id_direccion = $oDireccion->getId_direccion();
            $a_pkey = array('id_ubi' => $Qid_ubi, 'id_direccion' => $id_direccion);
        } else {
            // puede haber más de una dirección
            $a_id_direccion = explode(',', $_POST['id_direccion']);
            $obj = 'ubis\\model\\entity\\' . $Qobj_dir;
            $oDireccion = new $obj($a_id_direccion[$Qidx]);
            $a_pkey = array('id_ubi' => $Qid_ubi, 'id_direccion' => $a_id_direccion[$Qidx]);
            guardarObjeto($oDireccion, $campos_chk);
        }

        switch ($Qobj_dir) {
            case "DireccionCtrDl":
                $xDireccion = new CtrDlxDireccion($a_pkey);
                break;
            case "DireccionCtrEx":
                $xDireccion = new CtrExxDireccion($a_pkey);
                break;
            case "DireccionCdcDl":
                $xDireccion = new CdcDlxDireccion($a_pkey);
                break;
            case "DireccionCdcEx":
                $xDireccion = new CdcExxDireccion($a_pkey);
                break;
        }
        if (!empty($Qpropietario)) {
            $xDireccion->setPropietario('t');
        } else {
            $xDireccion->setPropietario('f');
        }
        if (!empty($Qprincipal)) {
            $xDireccion->setPrincipal('t');
        } else {
            $xDireccion->setPrincipal('f');
        }
        $xDireccion->DBGuardar();
        break;
}


function guardarObjeto($oObjeto, $campos_chk)
{
    $oObjeto->DBCarregar();
    $oDbl = $oObjeto->getoDbl();
    $cDatosCampo = $oObjeto->getDatosCampos();
    $a_values_o = [];
    foreach ($cDatosCampo as $oDatosCampo) {
        $camp = $oDatosCampo->getNom_camp();
        $valor = empty($_POST[$camp]) ? '' : $_POST[$camp];
        if ($oDatosCampo->datos_campo($oDbl, 'tipo') === "bool") { //si es un campo boolean, cambio los valores on, off... por true, false...
            if ($valor === "on") {
                $valor = 't';
                $a_values_o[$camp] = $valor;
            } else {
                // compruebo que esté en la lista de campos enviados
                if (in_array($camp, $campos_chk)) {
                    $valor = 'f';
                    $a_values_o[$camp] = $valor;
                }
            }
            // Si es un centro los valores sf/sv no se pueden cambiar
            $classname = get_class($oObjeto);
            $obj_pau = substr($classname, strrpos($classname, '\\') + 1);
            if ($obj_pau === 'CentroDl' || $obj_pau === 'CentroEx') {
                switch (ConfigGlobal::mi_sfsv()) {
                    case 1: // sv
                        $a_values_o['sv'] = 't';
                        break;
                    case 2: //sf
                        $a_values_o['sf'] = 't';
                        break;
                }
            }
        } else {
            if (!isset($_POST[$camp])) {
                continue;
            }
            //cuando el campo es tipo_labor, se pasa un array que hay que convertirlo en número.
            if ($camp === "tipo_labor") {
                $byte = 0;
                foreach ($_POST[$camp] as $bit) {
                    $byte = $byte + $bit;
                }
                $valor = $byte;
            }
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
    $oObjeto->setAllAtributes($a_values_o, TRUE);

    if ($oObjeto->DBGuardar() === false) {
        $msg_err = _("hay un error, no se ha guardado");
    }

    if (!empty($msg_err)) {
        echo $msg_err;
    }
}