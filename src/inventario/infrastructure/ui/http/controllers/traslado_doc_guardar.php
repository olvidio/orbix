<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\shared\web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_ubi_new = input_int($_POST, 'id_ubi_new');
$Qid_lugar_new = input_int($_POST, 'id_lugar_new');

$error_txt = '';

/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
if (!empty($Qid_ubi_new)) {
    foreach ($a_sel as $id_doc) {
        $oDocumento = $DocumentoRepository->findById((int) $id_doc);
        if ($oDocumento === null) {
            continue;
        }
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
        $oDocumento = $DocumentoRepository->findById((int) $id_doc);
        if ($oDocumento === null) {
            continue;
        }
        $oDocumento->setIdUbiVo(null);
        if ($DocumentoRepository->Guardar($oDocumento) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $DocumentoRepository->getErrorTxt();
        }
    }
}

ContestarJson::enviar($error_txt, 'ok');

