<?php

use src\inventario\application\repositories\DocumentoRepository;
use src\inventario\domain\entity\Documento;
use web\ContestarJson;
use web\DateTimeLocal;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_tipo_doc = (string)filter_input(INPUT_POST, 'id_tipo_doc');
$Qnumerado = (string)filter_input(INPUT_POST, 'numerado');
$Qstr_selected_id = (string)filter_input(INPUT_POST, 'str_selected_id');
$Qf_recibido = (string)filter_input(INPUT_POST, 'f_recibido');
$Qf_asignado = (string)filter_input(INPUT_POST, 'f_asignado');


$selected_id = json_decode(rawurldecode($Qstr_selected_id));
$error_txt = '';


$DocumentoRepository = new DocumentoRepository();
$i = 0;
foreach ($selected_id as $id_ubi) {
    $var_num = "num_" . $id_ubi;
    $num = $_POST[$var_num];
    $cDocumentos = $DocumentoRepository->getDocumentos(['id_ubi' => $id_ubi, 'id_tipo_doc' => $Qid_tipo_doc]);
    if (empty($cDocumentos)) {
        $DocumentoRepository = new DocumentoRepository();
        $id_new = $DocumentoRepository->getNewId();
        $oDocumento = new Documento();
        $oDocumento->setId_doc($id_new);
        $oDocumento->setId_ubi($id_ubi);
        $oDocumento->setId_tipo_doc($Qid_tipo_doc);
    } else {
        $oDocumento = $cDocumentos[0];
    }

    if (!empty($Qnumerado)) {
        $oDocumento->setNum_reg($num);
    } else {
        $oDocumento->setNum_ejemplares($num);
    }

    // Si está vacío no hago nada (dejo lo que esté)
    if (!empty($Qf_recibido)) {
        $oF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);
        $oDocumento->setF_recibido($oF_recibido);
    }

    if (!empty($Qf_asignado)) {
        $oF_asignado = DateTimeLocal::createFromLocal($Qf_asignado);
        $oDocumento->setF_asignado($oF_asignado);
    }

    if ($DocumentoRepository->Guardar($oDocumento) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $DocumentoRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');

