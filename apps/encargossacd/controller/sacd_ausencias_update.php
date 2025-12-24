<?php

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;

/**
 * Esta página actualiza la base de datos de los encargos del sacd (ausencias).
 *
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        28/03/07.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
//


function modifica_sacd_ausencias($id_item, $id_enc, $id_nom, $f_ini, $f_fin)
{
    $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
    $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);

    $oEncargoSacd = $EncargoSacdRepository->findById($id_item);

    if (empty($f_ini) && empty($f_fin)) {
        if ($EncargoSacdRepository->Eliminar($oEncargoSacd) === false) {
            echo _("hay un error, no se ha eliminado");
            echo "\n" . $EncargoSacdRepository->getErrorTxt();
        }
    } else {
        $oEncargoSacd->setF_ini($f_ini);
        $oEncargoSacd->setF_fin($f_fin);
        if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $EncargoSacdRepository->getErrorTxt();
        }
        $aWhere = [
            'id_enc' => $id_enc,
            'id_nom' => $id_nom,
            'id_item_tarea_sacd' => $id_item,
        ];
        $cHorario = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere);
        foreach ($cHorario as $oHorario) {
            $oHorario->setF_ini($f_ini);
            $oHorario->setF_fin($f_fin);
            if ($EncargoSacdHorarioRepository->Guardar($oHorario) === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oHorario->getErrorTxt();
            }
        }
    }
}

function insert_sacd_ausencias($id_enc, $id_nom, $modo, $f_ini, $f_fin)
{
    $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
    $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);

    $newId = $EncargoSacdRepository->getNewId();
    $oEncargoSacd = new EncargoSacd();
    $oEncargoSacd->setId_item($newId);
    $oEncargoSacd->setId_enc($id_enc);
    $oEncargoSacd->setId_nom($id_nom);
    $oEncargoSacd->setModo($modo);
    $oEncargoSacd->setF_ini($f_ini);
    $oEncargoSacd->setF_fin($f_fin);
    if ($EncargoSacdRepository->Guardar($oEncargoSacd) === false) {
        echo _("hay un error, no se ha guardado");
        echo "\n" . $EncargoSacdRepository->getErrorTxt();
    }
    $id_item_tarea_sacd = $oEncargoSacd->getId_item();
    $oHorario = $EncargoSacdHorarioRepository->findById($id_item_tarea_sacd);
    if ($oHorario === null) {
        $newId = $EncargoSacdHorarioRepository->getNewId();
        $oHorario = new EncargoSacdHorario();
        $oHorario->setId_item($newId);
    }
    $oHorario->setId_item_tarea_sacd($id_item_tarea_sacd);
    $oHorario->setId_enc($id_enc);
    $oHorario->setId_nom($id_nom);
    $oHorario->setF_ini($f_ini);
    $oHorario->setF_fin($f_fin);
    if ($EncargoSacdHorarioRepository->Guardar($oHorario) === false) {
        echo _("hay un error, no se ha guardado");
        echo "\n" . $EncargoSacdHorarioRepository->getErrorTxt();
    }
}


$Qenc_num = (integer)filter_input(INPUT_POST, 'enc_num');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');

$Qa_inicio = (array)filter_input(INPUT_POST, 'inicio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qa_fin = (array)filter_input(INPUT_POST, 'fin', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qa_id_enc = (array)filter_input(INPUT_POST, 'id_enc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qa_id_item = (array)filter_input(INPUT_POST, 'id_item', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

//modificar 
for ($i = 0; $i < $Qenc_num; $i++) {
    // si no hay fecha fin, considero que es un dia y pongo la misma fecha que el inicio
    if (empty($Qa_fin[$i])) {
        $Qa_fin[$i] = $Qa_inicio[$i];
    }
    if (empty($Qa_id_item[$i])) {
        insert_sacd_ausencias($Qa_id_enc[$i], $Qid_nom, 2, $Qa_inicio[$i], $Qa_fin[$i]);
    } else { // si ya existe se modifica
        modifica_sacd_ausencias($Qa_id_item[$i], $Qa_id_enc[$i], $Qid_nom, $Qa_inicio[$i], $Qa_fin[$i]);
    }
}
