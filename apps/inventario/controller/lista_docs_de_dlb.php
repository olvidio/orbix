<?php

use inventario\domain\repositories\LugarRepository;
use inventario\domain\repositories\TipoDocRepository;
use inventario\domain\repositories\UbiInventarioRepository;
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
$cUbisInventario = $UbiInventarioRepository->getUbisInventarioLugar(TRUE);

$LugarRepository = new LugarRepository();
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
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
