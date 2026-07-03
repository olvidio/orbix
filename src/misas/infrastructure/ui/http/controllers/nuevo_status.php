<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\NuevoStatusPeriodo;
use src\shared\web\ContestarJson;

$Qid_zona = (int)FilterPostGet::post('id_zona', FILTER_VALIDATE_INT);
$Qperiodo = (string)FilterPostGet::post('periodo');
$Qempiezamin = (string)FilterPostGet::post('empiezamin');
$Qempiezamax = (string)FilterPostGet::post('empiezamax');
$Qestado = (int)FilterPostGet::post('estado', FILTER_VALIDATE_INT);

/** @var NuevoStatusPeriodo $useCase */
$useCase = DependencyResolver::get(NuevoStatusPeriodo::class);
$result = $useCase->execute($Qid_zona, $Qperiodo, $Qempiezamin, $Qempiezamax, $Qestado);

ContestarJson::enviar($result['error'], []);
