<?php

use src\misas\application\NuevoStatusPeriodo;
use web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qestado = (int)filter_input(INPUT_POST, 'estado');

$result = NuevoStatusPeriodo::execute($Qid_zona, $Qperiodo, $Qempiezamin, $Qempiezamax, $Qestado);

ContestarJson::enviar((string)($result['error'] ?? ''), []);
