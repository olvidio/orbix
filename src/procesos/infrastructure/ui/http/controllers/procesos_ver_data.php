<?php

use src\procesos\application\ProcesosVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosVerData $useCase */
$useCase = DependencyResolver::get(ProcesosVerData::class);

$Qmod = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'mod');
$Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item');

ContestarJson::enviar('', $useCase->execute($Qmod, $Qid_item));
