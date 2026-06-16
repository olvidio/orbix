<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona', FILTER_VALIDATE_INT);
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd', FILTER_VALIDATE_INT);

header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::getContent('/src/misas/zona_sacd_datos_get', [
    'id_zona' => $Qid_zona,
    'id_sacd' => $Qid_sacd,
]);
