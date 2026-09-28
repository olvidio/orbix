<?php

/**
 * Endpoint backend: emite `ctx_asignar_auto` (HashB) y la fecha ISO de
 * inicio de curso des que autoriza `sacd_asignar_auto`.
 */

use src\actividadessacd\application\SacdAsignarAutoFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdAsignarAutoFormData $useCase */
$useCase = DependencyResolver::get(SacdAsignarAutoFormData::class);
ContestarJson::enviar('', $useCase->execute());
