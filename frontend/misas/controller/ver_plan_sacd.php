<?php

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$Qid_sacd_key = (string)filter_input(INPUT_POST, 'id_sacd');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$data = PostRequest::getDataFromUrl('/src/misas/ver_plan_sacd_data', [
    'id_sacd' => $Qid_sacd_key,
    'periodo' => $Qperiodo,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
]);

header('Content-Type: text/html; charset=UTF-8');
echo $data['html'] ?? '';
