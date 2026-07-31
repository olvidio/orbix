<?php

/**
 * Endpoint JSON: listado solo lectura de alumnos/notas de un acta (acta_ver).
 */

use src\notas\application\ActaVerNotasListadoData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActaVerNotasListadoData $useCase */
$useCase = DependencyResolver::get(ActaVerNotasListadoData::class);
ContestarJson::enviar('', $useCase->execute($_POST));
