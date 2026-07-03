<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerPlanSacdData;
use src\shared\web\ContestarJson;

$Qid_sacd_key = (string)\src\shared\domain\helpers\FilterPostGet::post('id_sacd');
$Qperiodo = (string)\src\shared\domain\helpers\FilterPostGet::post('periodo');
$Qempiezamin = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin');
$Qempiezamax = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax');

/** @var VerPlanSacdData $useCase */
$useCase = DependencyResolver::get(VerPlanSacdData::class);
$result = $useCase->getData($Qid_sacd_key, $Qperiodo, $Qempiezamin, $Qempiezamax);
ContestarJson::enviar('', $result);
