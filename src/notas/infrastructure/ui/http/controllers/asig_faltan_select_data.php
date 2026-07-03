<?php

use src\notas\application\AsigFaltanSelectTablaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(AsigFaltanSelectTablaData::class))->execute([
        'numero' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'numero'),
        'b_c' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'b_c'),
        'c1' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'c1'),
        'c2' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'c2'),
        'personas_n' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'personas_n'),
        'personas_agd' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'personas_agd'),
        'lista' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'lista'),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
