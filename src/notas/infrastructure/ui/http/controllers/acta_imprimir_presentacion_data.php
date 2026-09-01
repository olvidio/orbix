<?php

use src\notas\application\ActaImprimirPresentacionData;
use src\notas\application\ActaMarcarImpresa;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $acta = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'acta');
    $mode = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'mode', 'imprimir');
    if ($mode !== 'mpdf') {
        $mode = 'imprimir';
    }
    $data = (DependencyResolver::get(ActaImprimirPresentacionData::class))->execute($acta, $mode);
    $errMarca = (DependencyResolver::get(ActaMarcarImpresa::class))->execute($acta);
    if ($errMarca !== '') {
        $data['errores'] = trim((string) ($data['errores'] ?? '') . "\n" . $errMarca);
    }
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
