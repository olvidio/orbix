<?php

use src\notas\application\ActaSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(ActaSelectData::class))->execute([
        'titulo' => input_string($_POST, 'titulo'),
        'acta' => input_string($_POST, 'acta'),
        'mes_fin_stgr' => input_int($_POST, 'mes_fin_stgr', 6),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
