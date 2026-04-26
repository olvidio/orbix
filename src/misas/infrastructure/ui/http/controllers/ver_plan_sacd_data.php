<?php

use src\misas\application\VerPlanSacdData;
use frontend\shared\web\ContestarJson;

$Qid_sacd_key = (string)filter_input(INPUT_POST, 'id_sacd');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

ContestarJson::enviar('', VerPlanSacdData::getData($Qid_sacd_key, $Qperiodo, $Qempiezamin, $Qempiezamax));
