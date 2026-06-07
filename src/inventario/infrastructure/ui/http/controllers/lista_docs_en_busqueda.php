<?php
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';

/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
$cDocumentos = $DocumentoRepository->getDocumentos(['en_busqueda' => 't']);

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);
$a_valores = [];
$a_nom = [];
$i = 0;
foreach ($cDocumentos as $oDocumento) {
    $i++;
    $id_ubi = $oDocumento->getId_ubi();
    $id_lugar = $oDocumento->getId_lugar();
    $num_reg = $oDocumento->getNum_reg();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();

    $oTipoDoc = $TipoDocRepository->findById((int) $id_tipo_doc);
    if ($oTipoDoc === null) {
        continue;
    }
    $nom_doc = $oTipoDoc->getNom_doc();
    $NombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';

    $oUbiDoc = $UbiInventarioRepository->findById((int) $id_ubi);
    if ($oUbiDoc === null) {
        continue;
    }
    $nom_ubi = $oUbiDoc->getNom_ubi();
    if (!empty($id_lugar)) {
        $oLugar = $LugarRepository->findById((int) $id_lugar);
        if ($oLugar === null) {
            continue;
        }
        $nom_ubi .= " --> " . $oLugar->getNom_lugar();
    }
    $a_valores[$i][1] = $nom_ubi;
    $a_valores[$i][2] = $NombreDoc;
    $a_valores[$i][3] = $num_reg;
    //para poder ordenar
    $a_nom[$i] = $a_valores[$i][1];
}
if (!empty($a_valores)) {
    array_multisort($a_nom, SORT_ASC, $a_valores);
}

$data = [
    'a_valores' => $a_valores,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
