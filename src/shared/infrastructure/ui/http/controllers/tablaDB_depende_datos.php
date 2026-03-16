<?php
use web\ContestarJson;

$Qclase_info_encoded = (string)filter_input(INPUT_POST, 'clase_info');
$QpKeyRepository = (string)filter_input(INPUT_POST, 'pKeyRepository');
$Qvalor_depende = (string)filter_input(INPUT_POST, 'valor_depende');

$opcion_sel = (string)filter_input(INPUT_POST, 'opcion_sel');
/***************  datos  **********************************/

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info_encoded);
$oDatos = new $obj();

$data['aOpciones'] = $oDatos->getOpcionesParaCondicion($QpKeyRepository,$Qvalor_depende,$opcion_sel);

$error_txt = '';

// envía una Response
ContestarJson::enviar($error_txt, $data);