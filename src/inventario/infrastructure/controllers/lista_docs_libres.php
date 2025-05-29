<?php

use src\inventario\application\repositories\DocumentoRepository;
use src\inventario\application\repositories\EgmRepository;
use src\inventario\application\repositories\EquipajeRepository;
use src\inventario\application\repositories\LugarRepository;
use src\inventario\application\repositories\WhereisRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qid_tipo_doc = (int)filter_input(INPUT_POST, 'id_tipo_doc');

$error_txt = '';

$EquipajeRepository = new EquipajeRepository();
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
$f_ini_iso = $oEquipaje->getF_ini()->getIso();
$f_fin_iso = $oEquipaje->getF_fin()->getIso();

$aEquipajes = $EquipajeRepository->getEquipajesCoincidentes($f_ini_iso, $f_fin_iso);

$EgmRepository = new EgmRepository();
$aEgms = $EgmRepository->getArrayIdFromIdEquipajes($aEquipajes);

$WhereisRepository = new WhereisRepository();
$aWhereis = $WhereisRepository->getArrayIdFromIdEgms($aEgms);

// selecciono todos y quito los ocupados
// dlb-Magatzem-->id_ubi=40
$id_ubi = 40;
$LugarRepository = new LugarRepository();
$DocumentoRepository = new DocumentoRepository();
$cDocumentos = $DocumentoRepository->getDocumentos(['id_tipo_doc' => $Qid_tipo_doc, 'id_ubi' => $id_ubi]);
$d = 0;
$a_valores = [];
foreach ($cDocumentos as $oDocumento) {
    $d++;
    $id_doc = $oDocumento->getId_doc();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();
    $identificador = $oDocumento->getIdentificador();
    $num_reg = $oDocumento->getNum_reg();
    $id_lugar = $oDocumento->getId_lugar();

    $oLugar = $LugarRepository->findById($id_lugar);
    $lugar = $oLugar->getNom_lugar();

    $a_valores[$d][0] = $id_doc;
    $a_valores[$d][1] = $lugar;
    $a_valores[$d][2] = $identificador;
}

$data = [
    'a_valores' => $a_valores,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
