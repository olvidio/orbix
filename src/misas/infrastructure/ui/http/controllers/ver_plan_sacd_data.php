<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerPlanSacdData;
use src\shared\web\ContestarJson;

$Qid_sacd_key = (string)FilterPostGet::post('id_sacd');
$Qperiodo = (string)FilterPostGet::post('periodo');
$Qempiezamin = (string)FilterPostGet::post('empiezamin');
$Qempiezamax = (string)FilterPostGet::post('empiezamax');

/** @var VerPlanSacdData $useCase */
$useCase = DependencyResolver::get(VerPlanSacdData::class);
$result = $useCase->getData($Qid_sacd_key, $Qperiodo, $Qempiezamin, $Qempiezamax);
ContestarJson::enviar('', $result);
