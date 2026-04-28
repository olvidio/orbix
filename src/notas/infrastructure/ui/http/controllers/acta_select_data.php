<?php

use src\notas\application\ActaSelectData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = ActaSelectData::execute([
        'titulo' => (string)($_POST['titulo'] ?? ''),
        'acta' => (string)($_POST['acta'] ?? ''),
        'mes_fin_stgr' => (int)($_POST['mes_fin_stgr'] ?? 6),
    ]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
