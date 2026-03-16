<?php

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_ubi_new = (int)filter_input(INPUT_POST, 'id_ubi_new');
$Qid_lugar_new = (int)filter_input(INPUT_POST, 'id_lugar_new');

$error_txt = '';

$DocumentoRepository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
if (!empty($Qid_ubi_new)) {
    foreach ($a_sel as $id_doc) {
        $oDocumento = $DocumentoRepository->findById($id_doc);
        $oDocumento->setIdUbiVo($Qid_ubi_new);
        if (!empty($Qid_lugar_new)) {
            $oDocumento->setId_lugar($Qid_lugar_new);
        }
        if ($DocumentoRepository->Guardar($oDocumento) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $DocumentoRepository->getErrorTxt();
        }
    }
} else {
    foreach ($a_sel as $id_doc) {
        $oDocumento = $DocumentoRepository->findById($id_doc);
        $oDocumento->setIdUbiVo('');
        if ($DocumentoRepository->Guardar($oDocumento) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $DocumentoRepository->getErrorTxt();
        }
    }
}

ContestarJson::enviar($error_txt, 'ok');

