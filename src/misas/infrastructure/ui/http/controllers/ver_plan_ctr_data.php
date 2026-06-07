<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\VerPlanCtrData;
use src\shared\web\ContestarJson;

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

/** @var VerPlanCtrData $useCase */
$useCase = DependencyResolver::get(VerPlanCtrData::class);
$result = $useCase->getData(
    $Qid_ubi,
    $Qperiodo,
    $Qempiezamin,
    $Qempiezamax,
);
ContestarJson::enviar('', $result);
