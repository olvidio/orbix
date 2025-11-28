<?php

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use web\ContestarJson;

$Qid_lugar = (integer)filter_input(INPUT_POST, 'id_lugar');
$error_txt = '';

$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$TipoDocRepository = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);
$DocumentoRepository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
$cDocumentos = $DocumentoRepository->getDocumentos(['id_lugar' => $Qid_lugar]);

$oLugar = $LugarRepository->findById($Qid_lugar);
$nombre_valija = $oLugar->getNom_lugar();
$d = 0;
$a_valores = [];
foreach ($cDocumentos as $oDocumento) {
    $d++;
    $id_doc = $oDocumento->getId_doc();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();
    $identificador = $oDocumento->getIdentificador();
    $num_reg = $oDocumento->getNum_reg();

    $oTipoDoc = $TipoDocRepository->findById($id_tipo_doc);
    $a_valores[$d]['sel'] = ['id' => $id_doc, 'select' => 'checked'];
    $a_valores[$d][1] = $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc();
    $a_valores[$d][2] = $identificador;
    $a_valores[$d][3] = $num_reg;
}

$data = [
    'a_valores' => $a_valores,
    'nombre_valija' => $nombre_valija,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
