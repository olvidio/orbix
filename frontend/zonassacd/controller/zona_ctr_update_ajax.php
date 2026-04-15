<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$Qid_zona_new = (string)filter_input(INPUT_POST, 'id_zona_new');
$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAsel = empty($QAsel) ? [] : $QAsel;

$data = PostRequest::getDataFromUrl('/src/zonassacd/zona_ctr_update', [
    'id_zona_new' => $Qid_zona_new,
    'sel' => $QAsel,
]);

echo $data['mensaje'] ?? '';
