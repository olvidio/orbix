<?php

// INICIO Cabecera global de URL de controlador *********************************

use encargossacd\model\entity\Encargo;
use ubis\model\entity\Ubi;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

$error_txt = '';
if (($Qque === 'modificar') || ($Qque === 'nuevo')) {
    $Qid_enc = (string)filter_input(INPUT_POST, 'id_enc');
    $Qid_tipo_enc = (string)filter_input(INPUT_POST, 'id_tipo_enc');
    $Qid_ubi = (string)filter_input(INPUT_POST, 'id_ubi');
    $Qorden = (string)filter_input(INPUT_POST, 'orden');
    $Qprioridad = (string)filter_input(INPUT_POST, 'prioridad');
    $Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
    $Qdescripcion_lugar = (string)filter_input(INPUT_POST, 'descripcion_lugar');
    $Qencargo = (string)filter_input(INPUT_POST, 'encargo');
    $Qidioma_enc = (string)filter_input(INPUT_POST, 'idioma_enc');
    $Qobserv = (string)filter_input(INPUT_POST, 'observ');
    $Qdia = (string)filter_input(INPUT_POST, 'dia');

    if (empty($Qid_enc)) { // nuevo
        $EncargoZona = new Encargo();
    } else {
        $EncargoZona = new Encargo($Qid_enc);
        $EncargoZona->DBCarregar();
    }
    $EncargoZona->setId_tipo_enc($Qid_tipo_enc);
    $EncargoZona->setsf_sv(8);
    $EncargoZona->setId_ubi($Qid_ubi);
    $EncargoZona->setOrden($Qorden);
    $EncargoZona->setPrioridad($Qprioridad);
    $EncargoZona->setId_zona($Qid_zona);
    $EncargoZona->setDesc_enc($Qencargo);
    $EncargoZona->setIdioma_enc($Qidioma_enc);
    $EncargoZona->setDesc_lugar($Qdescripcion_lugar);
    $EncargoZona->setObserv($Qobserv);
    if (!empty($Qid_ubi)) {
        $oUbi = Ubi::newUbi($Qid_ubi);
        $nombre_ubi = $oUbi->getNombre_ubi();
    } else {
        $nombre_ubi = '';
    }    

    $jsondata['lugar'] = $nombre_ubi;
    $jsondata['que'] = $Qque;

    if ($EncargoZona->DBGuardar() === FALSE) {
        $error_txt .= $EncargoZona->getErrorTxt();
    }
}

if ($Qque === 'borrar') {
    $Qid_enc = (string)filter_input(INPUT_POST, 'id_enc');
    $EncargoZona = new Encargo($Qid_enc);
    if ($EncargoZona->DBEliminar() === FALSE) {
        $error_txt .= $EncargoZona->getErrorTxt();
    }
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'Tot correcte.';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}
//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();
