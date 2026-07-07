<?php

declare(strict_types=1);

use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../FrontBootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$oPosicion = FrontBootstrap::boot();

$nRaw = filter_input(INPUT_POST, 'n', FILTER_VALIDATE_INT);
$n = is_int($nRaw) && $nRaw >= 1 ? $nRaw : 1;

$target = $oPosicion->nav()->backTarget($n);
if ($target === null) {
    echo json_encode([
        'url' => null,
        'parametros' => null,
        'bloque' => null,
    ], JSON_THROW_ON_ERROR);
    exit;
}

echo json_encode($target, JSON_THROW_ON_ERROR);
