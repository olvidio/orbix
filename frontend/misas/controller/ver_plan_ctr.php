<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$data = PostRequest::getDataFromUrl('/src/misas/ver_plan_ctr_data', [
    'id_zona' => $Qid_zona,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
]);

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_plan_ctr.phtml', [
    'columns' => $data['columns'] ?? [],
    'rows' => $data['rows'] ?? [],
    'legend' => $data['legend'] ?? [],
]);
