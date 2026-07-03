<?php
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$Qclase_info_encoded = (string)FilterPostGet::post('clase_info');
$QpKeyRepository = (string)FilterPostGet::post('pKeyRepository');
$Qvalor_depende = (string)FilterPostGet::post('valor_depende');

$opcion_sel = (string)FilterPostGet::post('opcion_sel');
/***************  datos  **********************************/

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info_encoded);
$oDatos = DatosInfoRepoResolver::resolve($obj);

$data['aOpciones'] = $oDatos->getOpcionesParaCondicion($QpKeyRepository,$Qvalor_depende,$opcion_sel);

$error_txt = '';

// envía una Response
ContestarJson::enviar($error_txt, $data);