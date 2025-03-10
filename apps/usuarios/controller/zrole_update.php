<?php

use menus\model\entity\GrupMenuRole;
use usuarios\model\entity\Role;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'del_grupmenu':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { //vengo de un checkbox
            foreach ($a_sel as $sel) {
                //$id_nom=$sel[0];
                $id_item = (integer)strtok($sel, "#");
                $oGrupMenuRole = new GrupMenuRole($id_item);
                if ($oGrupMenuRole->DBEliminar() === false) {
                    echo _("hay un error, no se ha eliminado");
                    echo "\n" . $oGrupMenuRole->getErrorTxt();
                }
            }
        }
        break;
    case 'add_grupmenu':
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { //vengo de un checkbox
            foreach ($a_sel as $sel) {
                //$id_nom=$sel[0];
                $id_role = strtok($sel, "#");
                $id_grupmenu = strtok("#");
                $oGrupMenuRole = new GrupMenuRole();
                $oGrupMenuRole->setId_role($id_role);
                $oGrupMenuRole->setId_grupmenu($id_grupmenu);
                if ($oGrupMenuRole->DBGuardar() === false) {
                    echo _("hay un error, no se ha guardado");
                    echo "\n" . $oGrupMenuRole->getErrorTxt();
                }
            }
        }
        break;
    case "guardar":
        $Qrole = (string)filter_input(INPUT_POST, 'role');
        $Qid_role = (integer)filter_input(INPUT_POST, 'id_role');
        $Qsf = (integer)filter_input(INPUT_POST, 'sf');
        $Qsv = (integer)filter_input(INPUT_POST, 'sv');
        $Qpau = (string)filter_input(INPUT_POST, 'pau');
        $Qdmz = (integer)filter_input(INPUT_POST, 'dmz');
        if ($Qrole) {
            $oRole = new Role(array('id_role' => $Qid_role));
            $oRole->setRole($Qrole);
            $sf = !empty($Qsf) ? '1' : 0;
            $oRole->setSf($sf);
            $sv = !empty($Qsv) ? '1' : 0;
            $oRole->setSv($sv);
            $oRole->setPau($Qpau);
            $dmz = !empty($Qdmz) ? '1' : 0;
            $oRole->setDmz($dmz);
            if ($oRole->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oRole->getErrorTxt();
            }
        } else {
            exit("debe poner un nombre");
        }
        break;
    case "nuevo":
        $Qrole = (string)filter_input(INPUT_POST, 'role');
        $Qsf = (integer)filter_input(INPUT_POST, 'sf');
        $Qsv = (integer)filter_input(INPUT_POST, 'sv');
        $Qpau = (string)filter_input(INPUT_POST, 'pau');
        $Qdmz = (integer)filter_input(INPUT_POST, 'dmz');
        if ($Qrole) {
            $oRole = new Role();
            $oRole->setRole($Qrole);
            $sf = !empty($Qsf) ? '1' : 0;
            $oRole->setSf($sf);
            $sv = !empty($Qsv) ? '1' : 0;
            $oRole->setSv($sv);
            $oRole->setPau($Qpau);
            $dmz = !empty($Qdmz) ? '1' : 0;
            $oRole->setDmz($dmz);
            if ($oRole->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oRole->getErrorTxt();
            }
        } else {
            exit("debe poner un nombre");
        }
        break;
}