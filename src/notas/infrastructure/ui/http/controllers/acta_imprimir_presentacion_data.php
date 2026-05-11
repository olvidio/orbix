<?php

use src\notas\application\ActaImprimirPresentacionData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $acta = (string)($_POST['acta'] ?? '');
    $mode = (string)($_POST['mode'] ?? 'imprimir');
    if ($mode !== 'mpdf') {
        $mode = 'imprimir';
    }
    $data = ActaImprimirPresentacionData::execute($acta, $mode);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
