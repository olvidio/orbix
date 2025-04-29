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

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$error_txt = '';

// muestra los ctr que no tienen el documento.
$TipoDocRepository = new TipoDocRepository();
$oTipoDoc = $TipoDocRepository->findById($Qid_tipo_doc);
$nom_doc = $oTipoDoc->getNom_doc();
$nombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';

$DocumentoRepository = new DocumentoRepository();

$LugarRepository = new LugarRepository();
$UbiInventarioRepository = new UbiInventarioRepository();

$cTodosUbis = $UbiInventarioRepository->getUbisInventario();
$i = 0;
foreach ($cTodosUbis as $oUbiDoc) {
    $id_ubi = $oUbiDoc->getId_ubi();
    $nom_ubi = $oUbiDoc->getNom_ubi();

    $cDocumentos = $DocumentoRepository->getDocumentos(['id_tipo_doc' => $Qid_tipo_doc, 'id_ubi' => $id_ubi]);
    if (count($cDocumentos) > 0) {
        continue;
    }
    $i++;
    $a_valores[$i][1] = $nom_ubi;
    //para poder ordenar
    $a_nom[$i] = $a_valores[$i][1];
}
array_multisort($a_nom, SORT_ASC, $a_valores);

$a_cabeceras = array(ucfirst(_("centro")));
$a_botones = [];

$data = ['a_cabeceras' => $a_cabeceras,
    'a_botones' => $a_botones,
    'a_valores' => $a_valores,
    'nombreDoc' => $nombreDoc,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
