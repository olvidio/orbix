<?php

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
$cUbisInventario = $UbiInventarioRepository->getUbisInventarioLugar(false);
$i = 0;
foreach ($cUbisInventario as $oUbiInventario) {
    $i++;
    $id_ubi = $oUbiInventario->getId_ubi();
    $nom_ubi = $oUbiInventario->getNom_ubi();

    $a_valores[$i]['sel'] = $id_ubi;
    $a_valores[$i][1] = $nom_ubi;
    //para poder ordenar
    $a_nom[$i] = $a_valores[$i][1];
}
array_multisort($a_nom, SORT_ASC, $a_valores);

$data = [
    'a_valores' => $a_valores,
    'nombreDoc' => $nombreDoc,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
