<?php

use src\inventario\application\repositories\TipoDocRepository;
use src\inventario\application\repositories\UbiInventarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$Qinventario = (integer)filter_input(INPUT_POST, 'inventario');
$error_txt = '';


if (empty($Qinventario)) {
    $TipoDocRepository = new TipoDocRepository();
    $oTipoDoc = $TipoDocRepository->findById($Qid_tipo_doc);
    $nom_doc = $oTipoDoc->getNom_doc();
    $nombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';
} else {
    $nombreDoc = _("Para imprimir inventario");
}

$UbiInventarioRepository = new UbiInventarioRepository();
$cUbisInventario = $UbiInventarioRepository->getUbisInventarioLugar(FALSE);
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
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
