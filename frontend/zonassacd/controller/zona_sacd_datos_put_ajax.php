<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona', FILTER_VALIDATE_INT);
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd', FILTER_VALIDATE_INT);

header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::getContent('/src/misas/zona_sacd_datos_put', [
    'id_zona' => $Qid_zona,
    'id_sacd' => $Qid_sacd,
    'dw1' => (string)filter_input(INPUT_POST, 'dw1'),
    'dw2' => (string)filter_input(INPUT_POST, 'dw2'),
    'dw3' => (string)filter_input(INPUT_POST, 'dw3'),
    'dw4' => (string)filter_input(INPUT_POST, 'dw4'),
    'dw5' => (string)filter_input(INPUT_POST, 'dw5'),
    'dw6' => (string)filter_input(INPUT_POST, 'dw6'),
    'dw7' => (string)filter_input(INPUT_POST, 'dw7'),
]);
