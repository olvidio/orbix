<?php

use src\ubis\application\DelegacionesRegionStgrData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $region = (string)($_POST['region_stgr'] ?? '');
    if ($region === '') {
        throw new \RuntimeException(_('Se requiere region_stgr'));
    }
    $data = DelegacionesRegionStgrData::execute($region);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
