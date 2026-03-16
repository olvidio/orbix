<?php

use src\certificados\domain\CertificadoRecibidoDelete;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
} else {
    $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
}

$CertificadoRecibidoDelete = new CertificadoRecibidoDelete();
$error_txt = $CertificadoRecibidoDelete->delete($Qid_item);

// envía una Response
ContestarJson::enviar($error_txt, 'ok');