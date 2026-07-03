<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
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

AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', 'ver_plan_sacd.phtml', [
    'rows' => $data['rows'] ?? [],
]);
