<?php

use src\shared\domain\DatosFormRepo;
use web\ContestarJson;

$Qclase_info = filter_input(INPUT_POST, 'clase_info');
$a_pkey = filter_input(INPUT_POST, 'a_pkey');
$Qobj_pau = filter_input(INPUT_POST, 'obj_pau');
$Qmod = filter_input(INPUT_POST, 'mod');

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info);
$oInfoClase = new $obj();

$oInfoClase->setMod($Qmod);
$oInfoClase->setA_pkey($a_pkey);
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
    $oDatosFormRepo->setFicha($oFicha);
}

if (!empty($Qmod)) {
    $oDatosFormRepo->setMod($Qmod);
}

// Get the form data
$formData = $oDatosFormRepo->getFormularioData();

$error_txt = '';
$data = $formData;
$data['explicacion_txt'] = $explicacion_txt;
$data['tit_txt'] = $tit_txt;

ContestarJson::enviar($error_txt, $data);