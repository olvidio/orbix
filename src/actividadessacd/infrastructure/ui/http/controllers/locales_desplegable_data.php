<?php
/**
 * Endpoint backend: desplegable de locales para filtros sacd.
 */

use src\actividadessacd\application\LocalesDesplegableData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var LocalesDesplegableData $useCase */
$useCase = DependencyResolver::get(LocalesDesplegableData::class);
ContestarJson::enviar('', $useCase->execute());
