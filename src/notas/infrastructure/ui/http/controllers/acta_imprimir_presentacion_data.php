<?php

use src\notas\application\ActaImprimirPresentacionData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

$error = '';
$data = [];
try {
    $acta = input_string($_POST, 'acta');
    $mode = input_string($_POST, 'mode', 'imprimir');
    if ($mode !== 'mpdf') {
        $mode = 'imprimir';
    }
    $data = (DependencyResolver::get(ActaImprimirPresentacionData::class))->execute($acta, $mode);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
