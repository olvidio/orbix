<?php

// INICIO Cabecera global de URL de controlador *********************************

use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargoHorario;
use Illuminate\Http\JsonResponse;
use misas\domain\EncargoDiaId;
use misas\domain\EncargoDiaTend;
use misas\domain\EncargoDiaTstart;
use misas\domain\entity\EncargoDia;
use misas\domain\repositories\EncargoDiaRepository;
use web\DateTimeLocal;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Quuid_item = (string)filter_input(INPUT_POST, 'uuid_item');
$Qkey = (string)filter_input(INPUT_POST, 'key');
$Qtstart = (string)filter_input(INPUT_POST, 'tstart');
$Qtend = (string)filter_input(INPUT_POST, 'tend');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qdia = (string)filter_input(INPUT_POST, 'dia');

$error_txt = '';

if (empty($Quuid_item)) {
    exit("Error: falta el id_item");
}
$Uuid = new EncargoDiaId($Quuid_item);

$EncargoDiaRepository = new EncargoDiaRepository();
$oEncargoDia = $EncargoDiaRepository->findById($Uuid);
if ($oEncargoDia === null) {
    $oEncargoDia = new EncargoDia();
    $oEncargoDia->setUuid_item($Uuid);
    $oEncargoDia->setId_enc($Qid_enc);
}

$flag_borrado = FALSE;
if (empty($Qkey)) { // no hay ningún sacd
    if ($EncargoDiaRepository->Eliminar($oEncargoDia) === FALSE) {
        $error_txt .= $EncargoDiaRepository->getErrorTxt();
    }
    $flag_borrado = TRUE;
} else {
    $porciones = explode("#", $Qkey);
    $iniciales = $porciones[0];
    $id_nom = $porciones[1];
//    echo 'id nom: '.$id_nom.'<br>';

    $oEncargoDia->setId_nom($id_nom);

    if (empty($Qtstart) || empty($Qtend)) {
        // comprobar si es obligatorio
        $oEncargo = new Encargo($Qid_enc);
        $id_tipo_encargo = $oEncargo->getId_tipo_enc();
        $oTipoEncargo = new EncargoTipo($id_tipo_encargo);
        $modo_horario = $oTipoEncargo->getMod_horario();
        if ($modo_horario === EncargoTipo::HORARIO_POR_HORAS) {
            $oDia = new DateTimeLocal($Qdia);
            $dia_week = $oDia->format('N'); // N: 1 (para lunes) hasta 7 (para domingo)
            $h_ini = '';
            $h_fin = '';
            $aWhere = [
                'dia_ref' => "$dia_week|A",
                'id_enc' => $Qid_enc,
                'f_ini' => $Qdia,
                'f_fin' => 'x',
            ];
            $aOperador = [
                'dia_ref' => '~',
                'f_ini' => '<=',
                'f_fin' => 'IS NULL'
            ];
            $gesEncargoHorario = new GestorEncargoHorario();
            $cEncargoHorarios1 = $gesEncargoHorario->getEncargoHorarios($aWhere, $aOperador);
            // añadir los que tienen f_fin pero en un futuro:
            $aWhere['f_fin'] = $Qdia;
            $aOperador['f_fin'] = '>=';
            $cEncargoHorarios2 = $gesEncargoHorario->getEncargoHorarios($aWhere, $aOperador);
            $cEncargoHorarios = array_merge($cEncargoHorarios1, $cEncargoHorarios2);
            // TODO si hay varios?¿?¿
            if (count($cEncargoHorarios) > 0) {            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $EncargoDiaRepository = new EncargoDiaRepository();
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

            }
            if (empty($Qtstart) && !empty($h_ini)) {
                $Qtstart = $h_ini;
            }
            if (empty($Qtend) && !empty($h_fin)) {
                $Qtend = $h_fin;
            }
        }
        // poner por defecto el del encargo

        //$Qtstart = (new DateTimeLocal(''))->format('H:i');
    }

    $QTstart = new EncargoDiaTstart($Qdia, $Qtstart);
    $oEncargoDia->setTstart($QTstart);

    $QTend = new EncargoDiaTend($Qdia, $Qtend);
    $oEncargoDia->setTend($QTend);

    $oEncargoDia->setObserv($Qobserv);


    if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
        $error_txt .= $EncargoDiaRepository->getErrorTxt();
    }
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    if ($flag_borrado) {
        $a_meta = [
            'uuid-item' => '',
            'key' => '',
            'tstart' => '',
            'tend' => '',
            'observ' => ''
        ];
    } else {
        $a_meta = [
            'uuid-item' => $Quuid_item,
            'key' => $Qkey,
            'tstart' => $QTstart->getHora(),
            'tend' => $QTend->getHora(),
            'observ' => $Qobserv
        ];
    }
    $jsondata['meta'] = $a_meta;
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = 'ERROR: '.$error_txt;
}

(new JsonResponse($jsondata))->send();
exit();
