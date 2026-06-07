<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_tipo_doc = input_int($_POST, 'id_tipo_doc');
$Qinventario = input_int($_POST, 'inventario');
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
$cUbisInventario = $UbiInventarioRepository->getUbisInventarioLugar(TRUE);

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
$a_valores = [];
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
