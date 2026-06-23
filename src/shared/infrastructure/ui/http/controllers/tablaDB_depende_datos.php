<?php
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\web\ContestarJson;

$Qclase_info_encoded = (string)filter_post('clase_info');
$QpKeyRepository = (string)filter_post('pKeyRepository');
$Qvalor_depende = (string)filter_post('valor_depende');

$opcion_sel = (string)filter_post('opcion_sel');
/***************  datos  **********************************/

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info_encoded);
$oDatos = DatosInfoRepoResolver::resolve($obj);

$data['aOpciones'] = $oDatos->getOpcionesParaCondicion($QpKeyRepository,$Qvalor_depende,$opcion_sel);

$error_txt = '';

// envía una Response
ContestarJson::enviar($error_txt, $data);