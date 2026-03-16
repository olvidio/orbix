<?php

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use web\ContestarJson;

$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qid_tipo_doc = (int)filter_input(INPUT_POST, 'id_tipo_doc');

$error_txt = '';

$EquipajeRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
$f_ini_iso = $oEquipaje->getF_ini()->getIso();
$f_fin_iso = $oEquipaje->getF_fin()->getIso();

$aEquipajes = $EquipajeRepository->getEquipajesCoincidentes($f_ini_iso, $f_fin_iso);

$EgmRepository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
$aEgms = $EgmRepository->getArrayIdFromIdEquipajes($aEquipajes);

$WhereisRepository = $GLOBALS['container']->get(WhereisRepositoryInterface::class);
$aWhereis = $WhereisRepository->getArrayIdFromIdEgms($aEgms);

// selecciono todos y quito los ocupados
// dlb-Magatzem-->id_ubi=40
$id_ubi = 40;
$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$DocumentoRepository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
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
ContestarJson::enviar($error_txt, $data);
