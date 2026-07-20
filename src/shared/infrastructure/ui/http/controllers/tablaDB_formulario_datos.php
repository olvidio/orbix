<?php

use src\shared\domain\DatosFormRepo;
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$Qclase_info_encoded = \src\shared\domain\helpers\FilterPostGet::post('clase_info');
$a_pkey = \src\shared\domain\helpers\FilterPostGet::post('a_pkey');
if (is_string($a_pkey) && $a_pkey !== '') {
    $decoded = json_decode($a_pkey, true);
    // Aceptar PK escalar (p.ej. 5) y PK compuesta (objeto/array).
    if (json_last_error() === JSON_ERROR_NONE) {
        $a_pkey = $decoded;
    }
}
$Qobj_pau = \src\shared\domain\helpers\FilterPostGet::post('obj_pau');
$Qmod = \src\shared\domain\helpers\FilterPostGet::post('mod');
$mod = is_string($Qmod) ? $Qmod : '';

// Tiene que ser en dos pasos.
$obj = urldecode((string) $Qclase_info_encoded);
$oInfoClase = DatosInfoRepoResolver::resolve($obj);
if (method_exists($oInfoClase, 'setObj_pau')) {
    $oInfoClase->setObj_pau($Qobj_pau);
}
$oInfoClase->setMod($mod !== '' ? $mod : null);
$oInfoClase->setA_pkey($a_pkey);
$oFicha = $oInfoClase->getFicha();
$aCamposDepende = $oInfoClase->getArrayCamposDepende();
$aOpciones_txt = [];
foreach ($aCamposDepende as $pKeyRepository => $campoDepende) {
    if ($mod === 'nuevo') {
       $id_pau = '';
       $valor_campo_depende = '';
    } elseif ($oFicha === null) {
        $id_pau = '';
        $valor_campo_depende = '';
    } else {
        $bbb = 'get' . ucfirst($pKeyRepository);
        $id_pau = $oFicha->$bbb();
        $aaa = 'get' . ucfirst($campoDepende);
        $valor_campo_depende = $oFicha->$aaa();
    }
    $opciones = $oInfoClase->getOpcionesParaCondicion($campoDepende, $id_pau, $valor_campo_depende);
    $aOpciones_txt[$campoDepende] = $opciones ?? '';
}

$oDatosFormRepo = new DatosFormRepo();
$oDatosFormRepo->setFicha($oFicha);
$oDatosFormRepo->setArrayOpcionesTxt($aOpciones_txt);
$oDatosFormRepo->setMod($mod);

$tit_txt = $oInfoClase->getTxtTitulo();
$explicacion_txt = $oInfoClase->getTxtExplicacion();

$camposForm = $oDatosFormRepo->getCamposForm();
$camposNo = $oDatosFormRepo->getCamposNo();

// Set the properties
if (!empty($oFicha)) {
    $oDatosFormRepo->setFicha($oFicha);
}

if ($mod !== '') {
    $oDatosFormRepo->setMod($mod);
}

// Get the form data
$formData = $oDatosFormRepo->getFormularioData();

$error_txt = '';
$data = $formData;
$data['explicacion_txt'] = $explicacion_txt;
$data['tit_txt'] = $tit_txt;

ContestarJson::enviar($error_txt, $data);