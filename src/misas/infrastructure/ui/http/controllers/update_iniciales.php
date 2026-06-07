<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\UpdateIniciales;
use src\shared\web\ContestarJson;

$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd', FILTER_VALIDATE_INT);
$Qiniciales = (string)filter_input(INPUT_POST, 'iniciales');
$Qcolor = (string)filter_input(INPUT_POST, 'color');

/** @var UpdateIniciales $useCase */
$useCase = DependencyResolver::get(UpdateIniciales::class);
$result = $useCase->execute($Qid_sacd, $Qiniciales, $Qcolor);

ContestarJson::enviar($result, ['id_sacd' => $Qid_sacd]);
