<?php

// INICIO Cabecera global de URL de controlador *********************************

use encargossacd\model\entity\EncargoSacd;
use encargossacd\model\entity\EncargoSacdHorario;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoSacdHorario;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qtarea = (integer)filter_input(INPUT_POST, 'tarea');
$Qdia_ref = (string)filter_input(INPUT_POST, 'dia_ref');
$Qsemana = (integer)filter_input(INPUT_POST, 'semana');
$Qid_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qid_item_horario_sacd = (integer)filter_input(INPUT_POST, 'id_item_horario_sacd');

$f_ini_iso = '2023-12-01';
$f_fin_iso = '2024-01-01';
$error_txt = '';


/**
 * @param int $Qid_enc
 * @param int $Qid_sacd
 * @param string $f_ini_iso
 * @param string $f_fin_iso
 * @param string $error_txt
 * @return array
 */
function add_sacd_a_encargo(int $Qid_enc, int $Qid_sacd, string $f_ini_iso, string $f_fin_iso): array
{
    $aWhere = [
        'id_enc' => $Qid_enc,
        'id_nom' => $Qid_sacd,
    ];
    $gesEncargoSacd = new GestorEncargoSacd();
    $cEncargoSacd = $gesEncargoSacd->getEncargosSacd($aWhere);
    if (empty($cEncargoSacd)) {
        $oEncargoSacd = new EncargoSacd();
        $oEncargoSacd->setId_nom($Qid_sacd);
        $oEncargoSacd->setId_enc($Qid_enc);
        $oEncargoSacd->setModo(EncargoTipo::HORARIO_POR_HORAS);
        $oEncargoSacd->setF_ini($f_ini_iso, FALSE);
        $oEncargoSacd->setF_fin($f_fin_iso, FALSE);
        if ($oEncargoSacd->DBGuardar() === FALSE) {
            $error_txt = $oEncargoSacd->getErrorTxt();
        }
    } else {
        // debería haber solamente uno
        $oEncargoSacd = $cEncargoSacd[0];
    }
    // recuperar el id_item como id_item_tarea_sacd
    $id_item_tarea_sacd = $oEncargoSacd->getId_item();
    return array($error_txt?? '', $id_item_tarea_sacd);
}

if (empty($Qid_sacd) && !empty($Qid_item_horario_sacd)) { // quitar
    $oEncargoSacdHorario = new EncargoSacdHorario($Qid_item_horario_sacd);
    if ($oEncargoSacdHorario->DBEliminar() === FALSE) {
        $error_txt .= $oEncargoSacdHorario->getErrorTxt();
    }
} else {
    // nuevo
    if (empty($Qid_item_horario_sacd)) {
        // 1.- añadir como sacd del encargo (si no existe)
        list($error_txt, $id_item_tarea_sacd) = add_sacd_a_encargo($Qid_enc, $Qid_sacd, $f_ini_iso, $f_fin_iso);

        // 2.- añadir horario
        $h_ini = '';
        $h_fin = '';
        $oEncargoSacdHorario = new EncargoSacdHorario();
        $oEncargoSacdHorario->setId_enc($Qid_enc);
        $oEncargoSacdHorario->setId_nom($Qid_sacd);
        $oEncargoSacdHorario->setF_ini($f_ini_iso, FALSE);
        $oEncargoSacdHorario->setF_fin($f_fin_iso, FALSE);
        $oEncargoSacdHorario->setH_ini($h_ini);
        $oEncargoSacdHorario->setH_fin($h_fin);
        $oEncargoSacdHorario->setDia_ref($Qdia_ref);
        $oEncargoSacdHorario->setId_item_tarea_sacd($id_item_tarea_sacd);
        if ($oEncargoSacdHorario->DBGuardar() === FALSE) {
            $error_txt .= $oEncargoSacdHorario->getErrorTxt();
        }
    } else {
        // modificar horario
        // 1.- añadir como sacd del encargo (si no existe)
        list($error_txt, $id_item_tarea_sacd) = add_sacd_a_encargo($Qid_enc, $Qid_sacd, $f_ini_iso, $f_fin_iso);
        // 2.- modificar horario
        $oEncargoSacdHorario = new EncargoSacdHorario($Qid_item_horario_sacd);
        $oEncargoSacdHorario->DBCarregar();
        $oEncargoSacdHorario->setId_nom($Qid_sacd);
        $oEncargoSacdHorario->setId_item_tarea_sacd($id_item_tarea_sacd);
        if ($oEncargoSacdHorario->DBGuardar() === FALSE) {
            $error_txt .= $oEncargoSacdHorario->getErrorTxt();
        }
    }
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'ok';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}

//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();
