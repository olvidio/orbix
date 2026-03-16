<?php

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use web\ContestarJson;

$Qid_tipo_doc = (string)filter_input(INPUT_POST, 'id_tipo_doc');
$Qnumerado = (string)filter_input(INPUT_POST, 'numerado');
$Qstr_selected_id = (string)filter_input(INPUT_POST, 'str_selected_id');
$Qf_recibido = (string)filter_input(INPUT_POST, 'f_recibido');
$Qf_asignado = (string)filter_input(INPUT_POST, 'f_asignado');

$selected_id = json_decode(rawurldecode($Qstr_selected_id));
$error_txt = '';

$DocumentoRepository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$i = 0;
foreach ($selected_id as $id_lugar) {
    $oLugar = $LugarRepository->findById($id_lugar);
    $id_ubi = $oLugar->getId_ubi();
    $var_num = "num_" . $id_lugar;
    $num = $_POST[$var_num];
    $aWhere = ['id_ubi' => $id_ubi,
        'id_lugar' => $id_lugar,
        'id_tipo_doc' => $Qid_tipo_doc
    ];
    $cDocumentos = $DocumentoRepository->getDocumentos($aWhere);
    if (count($cDocumentos) === 1) {
        $oDocumento = $cDocumentos[0];
    } else {
        $error_txt .= _("No se encuentra el documento");
        $error_txt .= "\\n";
    }
    if (!empty($numerado)) {
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

