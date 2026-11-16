<?php

use src\procesos\application\FasesActivCambioTipoActividadData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FasesActivCambioTipoActividadData $useCase */
$useCase = DependencyResolver::get(FasesActivCambioTipoActividadData::class);

ContestarJson::enviar('', $useCase->execute($_POST));
