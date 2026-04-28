<?php

use frontend\shared\web\ContestarJson;
use src\certificados\application\CertificadoEmitidoUploadFirmadoFormData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];
try {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) {
        $Qid_item = (int)strtok((string)$a_sel[0], '#');
    } else {
        $Qid_item = (int)filter_input(INPUT_POST, 'id_item');
    }
    if ($Qid_item <= 0) {
        throw new \RuntimeException(_('certificado no válido'));
    }
    $data = CertificadoEmitidoUploadFirmadoFormData::execute($Qid_item);
    $data['id_item'] = $Qid_item;
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
