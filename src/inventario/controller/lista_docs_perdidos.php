<?php

use src\inventario\domain\repositories\DocumentoRepository;
use src\inventario\domain\repositories\LugarRepository;
use src\inventario\domain\repositories\TipoDocRepository;
use src\inventario\domain\repositories\UbiInventarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$DocumentoRepository = new DocumentoRepository();
$cDocumentos = $DocumentoRepository->getDocumentos(['perdido' => 't']);

$LugarRepository = new LugarRepository();
$TipoDocRepository = new TipoDocRepository();
$UbiInventarioRepository = new UbiInventarioRepository();
$i = 0;
foreach ($cDocumentos as $oDocumento) {
    $i++;
    $id_ubi = $oDocumento->getId_ubi();
    $id_lugar = $oDocumento->getId_lugar();
    $num_reg = $oDocumento->getNum_reg();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();
    $f_perdido = $oDocumento->getF_perdido()->getFromLocal();

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
    $a_valores[$i][4] = $f_perdido;
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
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
