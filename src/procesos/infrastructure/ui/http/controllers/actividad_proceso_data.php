<?php

use src\procesos\application\ActividadProcesoData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var ActividadProcesoData $useCase */
$useCase = DependencyResolver::get(ActividadProcesoData::class);

$Qid_activ = FuncTablasSupport::inputInt($_POST, 'id_activ');

ContestarJson::enviar('', $useCase->execute($Qid_activ));
