<?php

use src\shared\web\ContestarJson;
use src\certificados\application\CertificadoRecibidoModificarFormData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];
try {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qid_item = (int)strtok((string)($a_sel[0] ?? ''), '#');
    if ($Qid_item <= 0) {
        throw new \RuntimeException(_('certificado no válido'));
    }
    $data = CertificadoRecibidoModificarFormData::execute($Qid_item);
    $data['id_item'] = $Qid_item;
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
