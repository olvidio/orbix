<?php

use src\notas\application\AsigFaltanSelectTablaData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = AsigFaltanSelectTablaData::execute([
        'numero' => (int)($_POST['numero'] ?? 0),
        'b_c' => (string)($_POST['b_c'] ?? ''),
        'c1' => (string)($_POST['c1'] ?? ''),
        'c2' => (string)($_POST['c2'] ?? ''),
        'personas_n' => (string)($_POST['personas_n'] ?? ''),
        'personas_agd' => (string)($_POST['personas_agd'] ?? ''),
        'lista' => (string)($_POST['lista'] ?? ''),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
