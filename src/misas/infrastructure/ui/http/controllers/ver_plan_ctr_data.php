<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerPlanCtrData;
use src\shared\web\ContestarJson;

$Qid_ubi = (int)\src\shared\domain\helpers\FilterPostGet::post('id_ubi');
$Qperiodo = (string)\src\shared\domain\helpers\FilterPostGet::post('periodo');
$Qempiezamin = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin');
$Qempiezamax = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax');

/** @var VerPlanCtrData $useCase */
$useCase = DependencyResolver::get(VerPlanCtrData::class);
$result = $useCase->getData(
    $Qid_ubi,
    $Qperiodo,
    $Qempiezamin,
    $Qempiezamax,
);
ContestarJson::enviar('', $result);
