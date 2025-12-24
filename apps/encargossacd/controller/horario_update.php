<?php

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;
use web\DateTimeLocal;
use web\TimeLocal;

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

// asegurar tipo correcto para f_ini, f_fin
$oF_ini = empty($Qf_ini) ? null : new DateTimeLocal($Qf_ini);
$oF_fin = empty($Qf_fin) ? null : new DateTimeLocal($Qf_fin);
// asegurar tipo correcto para h_ini
$oH_ini = empty($Qh_ini) ? null : TimeLocal::fromString($Qh_ini);
$oH_fin = empty($Qh_fin) ? null : TimeLocal::fromString($Qh_fin);

if (empty($Qmas_menos)) $Qdia_ref = $Qdia;

$EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
switch ($Qmod) {
    case 'nuevo':
        //Compruebo que estén todos los campos necesarios
        if (empty($Qf_ini) || empty($Qdia)) {
            echo _("Debe llenar todos los campos que tengan un (*)") . "<br>";
            exit;
        }

        $NewId = $EncargoHorarioRepository->getNewId();
        $oEncargoHorario = new EncargoHorario();
        $oEncargoHorario->setId_item_h($NewId);
        $oEncargoHorario->setId_enc($Qid_enc);
        $oEncargoHorario->setF_ini($oF_ini);
        $oEncargoHorario->setF_fin($oF_fin);
        $oEncargoHorario->setDia_ref($Qdia_ref);
        $oEncargoHorario->setDia_num($Qdia_num);
        $oEncargoHorario->setMas_menos($Qmas_menos);
        $oEncargoHorario->setDia_inc($Qdia_inc);
        $oEncargoHorario->setH_ini($oH_ini);
        $oEncargoHorario->setH_fin($oH_fin);
        $oEncargoHorario->setN_sacd($Qn_sacd);
        $oEncargoHorario->setMes($Qmes);
        if ($EncargoHorarioRepository->Guardar($oEncargoHorario) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $EncargoHorarioRepository->getErrorTxt();
        }
        break;
    case "editar":
        //Compruebo que estén todos los campos necesarios
        if (empty($Qf_ini) || empty($Qdia)) {
            echo _("Debe llenar todos los campos que tengan un (*)") . "<br>";
            exit;
        }

        $oEncargoHorario = $EncargoHorarioRepository->findById($Qid_item_h);

        $oEncargoHorario->setF_ini($oF_ini);
        $oEncargoHorario->setF_fin($oF_fin);
        $oEncargoHorario->setDia_ref($Qdia_ref);
        $oEncargoHorario->setDia_num($Qdia_num);
        $oEncargoHorario->setMas_menos($Qmas_menos);
        $oEncargoHorario->setDia_inc($Qdia_inc);
        $oEncargoHorario->setH_ini($oH_ini);
        $oEncargoHorario->setH_fin($oH_fin);
        $oEncargoHorario->setN_sacd($Qn_sacd);
        $oEncargoHorario->setMes($Qmes);
        if ($EncargoHorarioRepository->Guardar($oEncargoHorario) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $EncargoHorarioRepository->getErrorTxt();
        }
        break;
    case "eliminar":
        if (!empty($_POST['sel_nom'])) {
            $id_item_h = strtok($_POST['sel_nom'][0], "#");
            $oEncargoHorario = $EncargoHorarioRepository->findById($Qid_item_h);
            $EncargoHorarioRepository->Elminar($oEncargoHorario);
        }
        break;
}

