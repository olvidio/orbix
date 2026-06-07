<?php

use src\procesos\application\FasesActivCambioTipoActividadHtmlData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FasesActivCambioTipoActividadHtmlData $useCase */
$useCase = DependencyResolver::get(FasesActivCambioTipoActividadHtmlData::class);

ContestarJson::enviar('', $useCase->execute($_POST));
