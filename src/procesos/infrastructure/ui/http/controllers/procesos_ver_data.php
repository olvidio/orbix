<?php

use src\procesos\application\ProcesosVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var ProcesosVerData $useCase */
$useCase = DependencyResolver::get(ProcesosVerData::class);

$Qmod = FuncTablasSupport::inputString($_POST, 'mod');
$Qid_item = FuncTablasSupport::inputInt($_POST, 'id_item');

ContestarJson::enviar('', $useCase->execute($Qmod, $Qid_item));
