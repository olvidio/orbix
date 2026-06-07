<?php
/**
 * Endpoint backend: encola mails de comunicacion de actividades a sacd.
 */

use src\actividadessacd\application\ComunicacionActividadesSacdEnviar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ComunicacionActividadesSacdEnviar $useCase */
$useCase = DependencyResolver::get(ComunicacionActividadesSacdEnviar::class);
ContestarJson::enviar($useCase->execute($_POST), 'ok');
