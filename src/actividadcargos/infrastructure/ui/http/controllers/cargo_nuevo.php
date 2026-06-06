<?php

use src\actividadcargos\application\ActividadCargoNuevo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Crea un `ActividadCargo`. Responde JSON `{success, mensaje, data}`.
 */
/** @var ActividadCargoNuevo $useCase */
$useCase = DependencyResolver::get(ActividadCargoNuevo::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
