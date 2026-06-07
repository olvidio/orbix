<?php

use src\procesos\application\ActividadProcesoData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

/** @var ActividadProcesoData $useCase */
$useCase = DependencyResolver::get(ActividadProcesoData::class);

$Qid_activ = input_int($_POST, 'id_activ');

ContestarJson::enviar('', $useCase->execute($Qid_activ));
