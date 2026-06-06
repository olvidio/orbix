<?php

use src\actividadcargos\application\ActividadCargoEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Elimina un `ActividadCargo` y, si procede, su `Asistente`.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var ActividadCargoEliminar $useCase */
$useCase = DependencyResolver::get(ActividadCargoEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
