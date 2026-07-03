<?php

use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_tipo_doc = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_doc');
$Qinventario = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'inventario');
$error_txt = '';

if (empty($Qinventario)) {
    /** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
    $oTipoDoc = $TipoDocRepository->findById($Qid_tipo_doc);
    if ($oTipoDoc === null) {
        ContestarJson::enviar($error_txt, []);
        return;
    }
    $nom_doc = $oTipoDoc->getNom_doc();
    $nombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';
} else {
    $nombreDoc = _("Para imprimir inventario");
}

/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);
$cUbisInventario = $UbiInventarioRepository->getUbisInventarioLugar(false);
$a_valores = [];
$a_nom = [];
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
