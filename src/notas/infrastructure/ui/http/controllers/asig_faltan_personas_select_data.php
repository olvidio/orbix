<?php

use src\notas\application\AsigFaltanPersonasSelectTablaData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = AsigFaltanPersonasSelectTablaData::execute([
        'id_asignatura' => (int)($_POST['id_asignatura'] ?? 0),
        'personas_n' => (string)($_POST['personas_n'] ?? ''),
        'personas_agd' => (string)($_POST['personas_agd'] ?? ''),
        'b_c' => (string)($_POST['b_c'] ?? ''),
        'c1' => (string)($_POST['c1'] ?? ''),
        'c2' => (string)($_POST['c2'] ?? ''),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
