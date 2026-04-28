<?php

use frontend\shared\web\ContestarJson;
use src\certificados\application\CertificadoEmitidoAdjuntarFormData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];
try {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $data = CertificadoEmitidoAdjuntarFormData::execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
