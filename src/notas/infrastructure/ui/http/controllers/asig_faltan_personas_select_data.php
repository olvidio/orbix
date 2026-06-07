<?php

use src\notas\application\AsigFaltanPersonasSelectTablaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(AsigFaltanPersonasSelectTablaData::class))->execute([
        'id_asignatura' => input_int($_POST, 'id_asignatura'),
        'personas_n' => input_string($_POST, 'personas_n'),
        'personas_agd' => input_string($_POST, 'personas_agd'),
        'b_c' => input_string($_POST, 'b_c'),
        'c1' => input_string($_POST, 'c1'),
        'c2' => input_string($_POST, 'c2'),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
