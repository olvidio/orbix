<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\VerPlanSacdData;
use src\shared\web\ContestarJson;

$Qid_sacd_key = (string)filter_post('id_sacd');
$Qperiodo = (string)filter_post('periodo');
$Qempiezamin = (string)filter_post('empiezamin');
$Qempiezamax = (string)filter_post('empiezamax');

/** @var VerPlanSacdData $useCase */
$useCase = DependencyResolver::get(VerPlanSacdData::class);
$result = $useCase->getData($Qid_sacd_key, $Qperiodo, $Qempiezamin, $Qempiezamax);
ContestarJson::enviar('', $result);
