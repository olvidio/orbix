<?php

use src\actividadcargos\application\ActividadCargoNuevo;
use src\actividadcargos\application\CargoMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Alta de un cargo en una actividad. La actividad y/o la persona que el
 * formulario ya conocía salen de la cápsula `ctx_guardar`; el resto del POST
 * (cargo, persona o actividad elegida, observaciones…) pasa tal cual.
 */
$input = CargoMutationCtx::aplicarGuardar($_POST, CargoMutationCtx::NUEVO);
if ($input === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
/** @var ActividadCargoNuevo $useCase */
$useCase = DependencyResolver::get(ActividadCargoNuevo::class);
$error_txt = $useCase->execute($input);
ContestarJson::enviar($error_txt, 'ok');
