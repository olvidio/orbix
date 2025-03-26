<?php

use inventario\domain\repositories\DocumentoRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_ubi_new = (int)filter_input(INPUT_POST, 'id_ubi_new');

$error_txt = '';

$DocumentoRepository = new DocumentoRepository();
if (!empty($Qid_ubi_new)) {
    foreach ($a_sel as $id_doc) {
        $oDocumento = $DocumentoRepository->findById($id_doc);
        $oDocumento->setId_ubi($Qid_ubi_new);
        if ($DocumentoRepository->Guardar($oDocumento) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $DocumentoRepository->getErrorTxt();
        }
    }
} else {
    foreach ($a_sel as $id_doc) {
        $oDocumento = $DocumentoRepository->findById($id_doc);
        $oDocumento->setId_ubi('');
        if ($DocumentoRepository->Guardar($oDocumento) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $DocumentoRepository->getErrorTxt();
        }
    }
}


ContestarJson::enviar($error_txt, 'ok');

