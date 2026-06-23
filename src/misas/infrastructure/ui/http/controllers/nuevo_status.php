<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\NuevoStatusPeriodo;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_post('id_zona', FILTER_VALIDATE_INT);
$Qperiodo = (string)filter_post('periodo');
$Qempiezamin = (string)filter_post('empiezamin');
$Qempiezamax = (string)filter_post('empiezamax');
$Qestado = (int)filter_post('estado', FILTER_VALIDATE_INT);

/** @var NuevoStatusPeriodo $useCase */
$useCase = DependencyResolver::get(NuevoStatusPeriodo::class);
$result = $useCase->execute($Qid_zona, $Qperiodo, $Qempiezamin, $Qempiezamax, $Qestado);

ContestarJson::enviar($result['error'], []);
