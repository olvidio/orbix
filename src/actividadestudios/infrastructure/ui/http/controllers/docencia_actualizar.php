<?php

use src\actividadestudios\application\DocenciaActualizar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocenciaActualizar $useCase */
$useCase = DependencyResolver::get(DocenciaActualizar::class);
$txt_rta = $useCase->execute($_POST);
ContestarJson::enviar('', $txt_rta);
