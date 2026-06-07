<?php

/**
 * Endpoint JSON: aplica traslado de centro/delegacion.
 */

use src\personas\application\TrasladoUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TrasladoUpdate $useCase */
$useCase = DependencyResolver::get(TrasladoUpdate::class);
$error_txt = $useCase->execute($_POST);

ContestarJson::enviar($error_txt, 'ok');
