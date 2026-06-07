<?php

use src\procesos\application\ProcesosVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/** @var ProcesosVerData $useCase */
$useCase = DependencyResolver::get(ProcesosVerData::class);

$Qmod = input_string($_POST, 'mod');
$Qid_item = input_int($_POST, 'id_item');

ContestarJson::enviar('', $useCase->execute($Qmod, $Qid_item));
