<?php

use src\notas\application\AsigFaltanSelectTablaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(AsigFaltanSelectTablaData::class))->execute([
        'numero' => FuncTablasSupport::inputInt($_POST, 'numero'),
        'b_c' => FuncTablasSupport::inputString($_POST, 'b_c'),
        'c1' => FuncTablasSupport::inputString($_POST, 'c1'),
        'c2' => FuncTablasSupport::inputString($_POST, 'c2'),
        'personas_n' => FuncTablasSupport::inputString($_POST, 'personas_n'),
        'personas_agd' => FuncTablasSupport::inputString($_POST, 'personas_agd'),
        'lista' => FuncTablasSupport::inputString($_POST, 'lista'),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
