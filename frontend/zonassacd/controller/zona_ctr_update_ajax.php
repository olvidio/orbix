<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_zona_new = (string)filter_input(INPUT_POST, 'id_zona_new');
$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAsel = empty($QAsel) ? [] : $QAsel;

header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::getContent('/src/zonassacd/zona_ctr_update', [
    'id_zona_new' => $Qid_zona_new,
    'sel' => $QAsel,
]);
