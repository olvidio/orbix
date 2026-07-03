<?php

use src\notas\application\AsigFaltanPersonasSelectTablaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(AsigFaltanPersonasSelectTablaData::class))->execute([
        'id_asignatura' => FuncTablasSupport::inputInt($_POST, 'id_asignatura'),
        'personas_n' => FuncTablasSupport::inputString($_POST, 'personas_n'),
        'personas_agd' => FuncTablasSupport::inputString($_POST, 'personas_agd'),
        'b_c' => FuncTablasSupport::inputString($_POST, 'b_c'),
        'c1' => FuncTablasSupport::inputString($_POST, 'c1'),
        'c2' => FuncTablasSupport::inputString($_POST, 'c2'),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
