<?php

use src\misas\application\VerPlanCtrData;
use web\ContestarJson;

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

ContestarJson::enviar('', VerPlanCtrData::getData(
    $Qid_ubi,
    $Qperiodo,
    $Qempiezamin,
    $Qempiezamax,
));
