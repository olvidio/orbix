<?php

use src\certificados\domain\CertificadoEmitidoEnviar;
use src\shared\web\ContestarJson;

// FIN de  Cabecera global de URL de controlador ****************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
} else {
    $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
}

$error_txt = CertificadoEmitidoEnviar::enviar($Qid_item);

// envía una Response
$jsondata = ContestarJson::enviar($error_txt, 'ok');