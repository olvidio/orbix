<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DelegacionesRegionStgrData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$error = '';
$data = [];
try {
    $region = FuncTablasSupport::inputString($_POST, 'region_stgr');
    if ($region === '') {
        throw new \RuntimeException(_('Se requiere region_stgr'));
    }
    $data = DependencyResolver::get(DelegacionesRegionStgrData::class)->execute($region);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
