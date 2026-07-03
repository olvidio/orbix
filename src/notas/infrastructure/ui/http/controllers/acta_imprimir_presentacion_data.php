<?php

use src\notas\application\ActaImprimirPresentacionData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$error = '';
$data = [];
try {
    $acta = FuncTablasSupport::inputString($_POST, 'acta');
    $mode = FuncTablasSupport::inputString($_POST, 'mode', 'imprimir');
    if ($mode !== 'mpdf') {
        $mode = 'imprimir';
    }
    $data = (DependencyResolver::get(ActaImprimirPresentacionData::class))->execute($acta, $mode);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
