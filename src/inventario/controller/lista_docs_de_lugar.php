<?php

use src\inventario\domain\repositories\DocumentoRepository;
use src\inventario\domain\repositories\LugarRepository;
use src\inventario\domain\repositories\TipoDocRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_lugar = (integer)filter_input(INPUT_POST, 'id_lugar');
$error_txt = '';

$LugarRepository = new LugarRepository();
$TipoDocRepository = new TipoDocRepository();
$DocumentoRepository = new DocumentoRepository();
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
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
