<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerPlanCtrData;
use src\shared\web\ContestarJson;

$Qid_ubi = (int)FilterPostGet::post('id_ubi');
$Qperiodo = (string)FilterPostGet::post('periodo');
$Qempiezamin = (string)FilterPostGet::post('empiezamin');
$Qempiezamax = (string)FilterPostGet::post('empiezamax');

/** @var VerPlanCtrData $useCase */
$useCase = DependencyResolver::get(VerPlanCtrData::class);
$result = $useCase->getData(
    $Qid_ubi,
    $Qperiodo,
    $Qempiezamin,
    $Qempiezamax,
);
ContestarJson::enviar('', $result);
