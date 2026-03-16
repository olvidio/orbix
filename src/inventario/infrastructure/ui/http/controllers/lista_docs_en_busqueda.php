<?php

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$DocumentoRepository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
$cDocumentos = $DocumentoRepository->getDocumentos(['en_busqueda' => 't']);

$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$TipoDocRepository = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);
$UbiInventarioRepository = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
$i = 0;
foreach ($cDocumentos as $oDocumento) {
    $i++;
    $id_ubi = $oDocumento->getId_ubi();
    $id_lugar = $oDocumento->getId_lugar();
    $num_reg = $oDocumento->getNum_reg();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();

    $oTipoDoc = $TipoDocRepository->findById($id_tipo_doc);
    $nom_doc = $oTipoDoc->getNom_doc();
    $NombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';

    $oUbiDoc = $UbiInventarioRepository->findById($id_ubi);
    $nom_ubi = $oUbiDoc->getNom_ubi();
    if (!empty($id_lugar)) {
        $oLugar = $LugarRepository->findById($id_lugar);
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
