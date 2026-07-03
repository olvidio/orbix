<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FuncTablasSupport;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_equipaje = FuncTablasSupport::inputInt($_POST, 'id_equipaje');
$Qid_tipo_doc = FuncTablasSupport::inputInt($_POST, 'id_tipo_doc');

$error_txt = '';

/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
if ($oEquipaje === null) {
    ContestarJson::enviar($error_txt, []);
    return;
}
$fIni = $oEquipaje->getF_ini();
$fFin = $oEquipaje->getF_fin();
$f_ini_iso = $fIni !== null ? $fIni->getIso() : '';
$f_fin_iso = $fFin !== null ? $fFin->getIso() : '';

$aEquipajes = $EquipajeRepository->getEquipajesCoincidentes($f_ini_iso, $f_fin_iso);

/** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
$aEgms = $EgmRepository->getArrayIdFromIdEquipajes($aEquipajes);

/** @var WhereisRepositoryInterface $WhereisRepository */
$WhereisRepository = DependencyResolver::get(WhereisRepositoryInterface::class);
$aWhereis = $WhereisRepository->getArrayIdFromIdEgms($aEgms);

// selecciono todos y quito los ocupados
// dlb-Magatzem-->id_ubi=40
$id_ubi = 40;
/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
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

    $oLugar = $LugarRepository->findById((int) $id_lugar);
    if ($oLugar === null) {
        continue;
    }
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
