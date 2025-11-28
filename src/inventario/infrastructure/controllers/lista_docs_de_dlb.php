<?php

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use web\ContestarJson;

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$Qinventario = (integer)filter_input(INPUT_POST, 'inventario');
$error_txt = '';

if (empty($Qinventario)) {
    $TipoDocRepository = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);
    $oTipoDoc = $TipoDocRepository->findById($Qid_tipo_doc);
    $nom_doc = $oTipoDoc->getNom_doc();
    $nombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';
} else {
    $nombreDoc = _("Para imprimir inventario");
}

$UbiInventarioRepository = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
$cUbisInventario = $UbiInventarioRepository->getUbisInventarioLugar(TRUE);

$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$cLugares =
$aGrupos = [];
$i = 0;
foreach ($cUbisInventario as $oUbiInventario) {
    $i++;
    $id_ubi = $oUbiInventario->getId_ubi();
    $aGrupos[$id_ubi] = $oUbiInventario->getNom_ubi();

    $cLugares = $LugarRepository->getLugares(['id_ubi' => $id_ubi, '_ordre' => 'nom_lugar']);
    $a = 0;
    foreach ($cLugares as $oLugar) {
        $a++;
        $a_valores[$id_ubi][$a]['sel'] = $oLugar->getId_lugar();
        if (!empty($Qinventario)) {
            $a_valores[$id_ubi][$a]['sel'] = ['id' => $oLugar->getId_lugar(), 'select' => ''];
        }
        $a_valores[$id_ubi][$a][1] = $oLugar->getNom_lugar();
    }
}

$data = [
    'a_valores' => $a_valores,
    'aGrupos' => $aGrupos,
    'nombreDoc' => $nombreDoc,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
