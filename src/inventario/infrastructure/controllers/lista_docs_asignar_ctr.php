<?php

use src\inventario\application\repositories\DocumentoRepository;
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
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$error_txt = '';

$TipoDocRepository = new TipoDocRepository();
$oTipoDoc = $TipoDocRepository->findById($Qid_tipo_doc);
$nom_doc = $oTipoDoc->getNom_doc();
$nombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';
$isNumerado = $oTipoDoc->isNumerado();

$DocumentoRepository = new DocumentoRepository();
if ($isNumerado) {
    //conseguir el último número.
    $ultimo = 0;
    $cDocumentos = $DocumentoRepository->getDocumentos(array('id_tipo_doc' => $Qid_tipo_doc, '_ordre' => 'num_reg DESC'));
    if (!empty($cDocumentos[0])) {
        $ultimo = $cDocumentos[0]->getNum_reg();
    }
}

$UbiInventarioRepository = new UbiInventarioRepository();
$cUbisInventario = $UbiInventarioRepository->getUbisInventario(['_ordre' => 'nom_ubi']);
$i = 0;
$sCamposForm = '';
foreach ($cUbisInventario as $oUbiInventario) {
    $id_ubi = $oUbiInventario->getId_ubi();
    $nom_ubi = $oUbiInventario->getNom_ubi();
    if (!in_array($id_ubi, $a_sel)) {
        continue;
    }
    $i++;
    $explicacion_txt ='';
    if ($isNumerado) {
        $num = $ultimo + $i;
        $explicacion_txt = ' <span class="explicacion_txt">'._("Ya es el número: último + 1").'</span>';
    } else {
        // es el número de ejemplares
        $cDocumentos = $DocumentoRepository->getDocumentos(array('id_tipo_doc' => $Qid_tipo_doc, 'id_ubi' => $id_ubi));
        if (!empty($cDocumentos[0])) {
            $num_ej = $cDocumentos[0]->getNum_ejemplares();
        } else {
            $num_ej = 1;
        }
        $num = $num_ej;
    }
    $a_valores[$i][1] = $nom_ubi;
    $a_valores[$i][2] = "<input type=text name='num_$id_ubi' size=5 value=$num>".$explicacion_txt;
    $sCamposForm .= empty($sCamposForm)? '' : '!';
    $sCamposForm .= "num_$id_ubi";
}

$data = [
    'a_valores' => $a_valores,
    'nombreDoc' => $nombreDoc,
    'isNumerado' => $isNumerado,
    'sCamposForm' => $sCamposForm,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
