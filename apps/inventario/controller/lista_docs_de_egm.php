<?php

use inventario\domain\repositories\DocumentoRepository;
use inventario\domain\repositories\EgmRepository;
use inventario\domain\repositories\LugarRepository;
use inventario\domain\repositories\TipoDocRepository;
use inventario\domain\repositories\WhereisRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item_egm = (integer)filter_input(INPUT_POST, 'id_item_egm');
$error_txt = '';

$LugarRepository = new LugarRepository();
$TipoDocRepository = new TipoDocRepository();
$DocumentoRepository = new DocumentoRepository();
$WhereisRepository = new WhereisRepository();
$EgmRepository = new EgmRepository();
$oEgm = $EgmRepository->findById($Qid_item_egm);
$id_lugar = $oEgm->getId_lugar();
$oLugar = $LugarRepository->findById($id_lugar);
$nombre_valija = $oLugar->getNom_lugar();


$cWhereis = $WhereisRepository->getWhereare(['id_item_egm' => $Qid_item_egm]);

$d = 0;
$a_valores = [];
$a_tipo = [];
foreach ($cWhereis as $oWhereis) {
    $d++;
    $id_item_whereis = $oWhereis->getId_item_whereis();
    $id_doc = $oWhereis->getId_doc();
    $oDocumento = $DocumentoRepository->findById($id_doc);
    //extract($oDocumento->getTot());
    $identificador = $oDocumento->getIdentificador();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();
    $oTipoDoc = $TipoDocRepository->findById($id_tipo_doc);
    $id_lugar_doc = $oDocumento->getId_lugar();
    $oLugar = $LugarRepository->findById($id_lugar_doc);
    $lugar = $oLugar->getNom_lugar();
    //$identificador = _("de") . " $lugar: $identificador";
    $identificador = $identificador;

    $a_valores[$d]['sel'] = $id_item_whereis;
    $a_valores[$d][1] = $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc();
    $a_valores[$d][2] = $identificador;
    //para poder ordenar
    $a_tipo[$d] = $a_valores[$d][1];
}
// ordenar por sigla
if (!empty($a_valores)) {
    array_multisort($a_tipo, SORT_ASC, $a_valores);
}

$data = [
    'a_valores' => $a_valores,
    'nombre_valija' => $nombre_valija,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
