<?php

use src\misas\application\NuevoStatusPeriodo;

require_once 'frontend/shared/global_header_front.inc';

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qestado = (int)filter_input(INPUT_POST, 'estado');

$result = NuevoStatusPeriodo::execute($Qid_zona, $Qperiodo, $Qempiezamin, $Qempiezamax, $Qestado);

header('Content-Type: text/html; charset=UTF-8');
if ($result['error'] !== '') {
    echo htmlspecialchars($result['error'], ENT_QUOTES, 'UTF-8');
} else {
    echo '';
}
