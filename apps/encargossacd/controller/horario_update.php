<?php

use encargossacd\model\entity\EncargoHorario;

/**
 * Esta página actualiza la base de datos de los encargos.
 *
 * Se le puede pasar la varaible $mod.
 *    Si es 'nuevo'  >> inserta un nuevo encargo.
 *    Si es 'editar' >> sólo cambia el tipo de encargo. Antes utiliza la función comprobar_cambio_tipo($id_activ,$valor)
 * que está en func_tablas.
 *   Si es 'eliminar' >> elimina.
 *
 * @package    delegacion
 * @subpackage    encargos
 * @author    Daniel Serrabou
 * @since        3/1/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qdia = (string)filter_input(INPUT_POST, 'dia');
$Qid_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qf_ini = (string)filter_input(INPUT_POST, 'f_ini');
$Qf_fin = (string)filter_input(INPUT_POST, 'f_fin');
$Qdia_ref = (integer)filter_input(INPUT_POST, 'dia_ref');
$Qdia_num = (integer)filter_input(INPUT_POST, 'dia_num');
$Qmas_menos = (string)filter_input(INPUT_POST, 'mas_menos');
$Qdia_inc = (integer)filter_input(INPUT_POST, 'dia_inc');
$Qh_ini = (string)filter_input(INPUT_POST, 'h_ini');
$Qh_fin = (string)filter_input(INPUT_POST, 'h_fin');
$Qn_sacd = (integer)filter_input(INPUT_POST, 'n_sacd');
$Qmes = (string)filter_input(INPUT_POST, 'mes');

if (empty($Qmas_menos)) $Qdia_ref = $Qdia;

switch ($Qmod) {
    case 'nuevo':
        //Compruebo que estén todos los campos necesasrios
        if (empty($Qf_ini) || empty($Qdia)) {
            echo _("Debe llenar todos los campos que tengan un (*)") . "<br>";
            exit;
        }

        $oEncargoHorario = new EncargoHorario();
        $oEncargoHorario->setId_enc($Qid_enc);
        $oEncargoHorario->setF_ini($Qf_ini);
        $oEncargoHorario->setF_fin($Qf_fin);
        $oEncargoHorario->setDia_ref($Qdia_ref);
        $oEncargoHorario->setDia_num($Qdia_num);
        $oEncargoHorario->setMas_menos($Qmas_menos);
        $oEncargoHorario->setDia_inc($Qdia_inc);
        $oEncargoHorario->setH_ini($Qh_ini);
        $oEncargoHorario->setH_fin($Qh_fin);
        $oEncargoHorario->setN_sacd($Qn_sacd);
        $oEncargoHorario->setMes($Qmes);
        if ($oEncargoHorario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oEncargoHorario->getErrorTxt();
        }
        break;
    case "editar":
        //Compruebo que estén todos los campos necesasrios
        if (empty($Qf_ini) || empty($Qdia)) {
            echo _("Debe llenar todos los campos que tengan un (*)") . "<br>";
            exit;
        }

        $oEncargoHorario = new EncargoHorario($Qid_item_h);
        $oEncargoHorario->DBCarregar();

        $oEncargoHorario->setF_ini($Qf_ini);
        $oEncargoHorario->setF_fin($Qf_fin);
        $oEncargoHorario->setDia_ref($Qdia_ref);
        $oEncargoHorario->setDia_num($Qdia_num);
        $oEncargoHorario->setMas_menos($Qmas_menos);
        $oEncargoHorario->setDia_inc($Qdia_inc);
        $oEncargoHorario->setH_ini($Qh_ini);
        $oEncargoHorario->setH_fin($Qh_fin);
        $oEncargoHorario->setN_sacd($Qn_sacd);
        $oEncargoHorario->setMes($Qmes);
        if ($oEncargoHorario->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oEncargoHorario->getErrorTxt();
        }
        break;
    case "eliminar":
        if (!empty($_POST['sel_nom'])) {
            $id_item_h = strtok($_POST['sel_nom'][0], "#");
            $oEncargoHorario = new EncargoHorario($id_item_h);
            $oEncargoHorario->DBEliminar();
        }
        break;
}

