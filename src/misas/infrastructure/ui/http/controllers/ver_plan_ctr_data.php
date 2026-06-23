<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\VerPlanCtrData;
use src\shared\web\ContestarJson;

$Qid_ubi = (int)filter_post('id_ubi');
$Qperiodo = (string)filter_post('periodo');
$Qempiezamin = (string)filter_post('empiezamin');
$Qempiezamax = (string)filter_post('empiezamax');

/** @var VerPlanCtrData $useCase */
$useCase = DependencyResolver::get(VerPlanCtrData::class);
$result = $useCase->getData(
    $Qid_ubi,
    $Qperiodo,
    $Qempiezamin,
    $Qempiezamax,
);
ContestarJson::enviar('', $result);
