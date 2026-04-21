<?php

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

$html = $data['html'] ?? '';
header('Content-Type: text/html; charset=UTF-8');
echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>'
    . htmlspecialchars(_('Plan de misas de un centro'), ENT_QUOTES, 'UTF-8')
    . '</title><style>body{font-family:sans-serif;margin:1em;} table{border-collapse:collapse;} th,td{border:1px solid #ccc;padding:4px;}</style></head><body>';
echo $html;
echo '<script>window.print();</script></body></html>';
