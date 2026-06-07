<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\NuevoStatusPeriodo;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona', FILTER_VALIDATE_INT);
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qestado = (int)filter_input(INPUT_POST, 'estado', FILTER_VALIDATE_INT);

/** @var NuevoStatusPeriodo $useCase */
$useCase = DependencyResolver::get(NuevoStatusPeriodo::class);
$result = $useCase->execute($Qid_zona, $Qperiodo, $Qempiezamin, $Qempiezamax, $Qestado);

ContestarJson::enviar($result['error'], []);
