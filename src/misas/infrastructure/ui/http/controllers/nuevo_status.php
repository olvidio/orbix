<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\NuevoStatusPeriodo;
use src\shared\web\ContestarJson;

$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona', FILTER_VALIDATE_INT);
$Qperiodo = (string)\src\shared\domain\helpers\FilterPostGet::post('periodo');
$Qempiezamin = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin');
$Qempiezamax = (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax');
$Qestado = (int)\src\shared\domain\helpers\FilterPostGet::post('estado', FILTER_VALIDATE_INT);

/** @var NuevoStatusPeriodo $useCase */
$useCase = DependencyResolver::get(NuevoStatusPeriodo::class);
$result = $useCase->execute($Qid_zona, $Qperiodo, $Qempiezamin, $Qempiezamax, $Qestado);

ContestarJson::enviar($result['error'], []);
