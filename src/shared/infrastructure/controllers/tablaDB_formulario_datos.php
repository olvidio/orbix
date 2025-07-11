<?php

use src\shared\domain\DatosFormRepo;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qclase_info = filter_input(INPUT_POST, 'clase_info');
$a_pkey = filter_input(INPUT_POST, 'a_pkey');
$Qobj_pau = filter_input(INPUT_POST, 'obj_pau');;
$Qmod = filter_input(INPUT_POST, 'mod');
//$QaOpciones_txt = (array)filter_input(INPUT_POST, 'aOpciones_txt', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info);
$oInfoClase = new $obj();

$oInfoClase->setMod($Qmod);
$oInfoClase->setA_pkey($a_pkey);
$oInfoClase->setObj_pau($Qobj_pau);
$oFicha = $oInfoClase->getFicha();
$aCamposDepende = $oInfoClase->getArrayCamposDepende();
$aOpciones_txt = [];
foreach ($aCamposDepende as $pKeyRepository => $campoDepende) {
    if ($Qmod === 'nuevo') {
       $id_pau = '';
       $valor_campo_depende = '';
    } else {
        $bbb = 'get' . ucfirst($pKeyRepository);
        $id_pau = $oFicha->$bbb();
        $aaa = 'get' . ucfirst($campoDepende);
        $valor_campo_depende = $oFicha->$aaa();
    }
    $aOpciones_txt[$campoDepende] = $oInfoClase->getOpcionesParaCondicion($campoDepende,$id_pau,$valor_campo_depende);
}

$oDatosFormRepo = new DatosFormRepo();
$oDatosFormRepo->setFicha($oFicha);
$oDatosFormRepo->setArrayOpcionesTxt($aOpciones_txt);
$oDatosFormRepo->setMod($Qmod);

$tit_txt = $oInfoClase->getTxtTitulo();
$explicacion_txt = $oInfoClase->getTxtExplicacion();

$camposForm = $oDatosFormRepo->getCamposForm();
$camposNo = $oDatosFormRepo->getCamposNo();

// Set the properties
if (!empty($oFicha)) {
    //$oDatosFormRepo->setFicha(unserialize($QoFicha, ['allowed_classes' => true]));
    $oDatosFormRepo->setFicha($oFicha);
}

if (!empty($Qmod)) {
    $oDatosFormRepo->setMod($Qmod);
}

/*
if (!empty($QaOpciones_txt)) {
    $oDatosFormRepo->setArrayOpcionesTxt($QaOpciones_txt);
}
*/

// Get the form data
$formData = $oDatosFormRepo->getFormularioData();

$error_txt = '';
$data = $formData;
$data['explicacion_txt'] = $explicacion_txt;
$data['tit_txt'] = $tit_txt;


ContestarJson::enviar($error_txt, $data);
