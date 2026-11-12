<?php

use src\actividadcargos\application\ActividadCargoEliminar;
use src\actividadcargos\application\CargoMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Elimina un `ActividadCargo` y, si procede, su `Asistente`.
 * El cargo y `elim_asis` salen de la cápsula `ctx_eliminar_cargo` de la fila
 * (la firman las listas 3102, 1302 y 3101). Responde JSON `{success, mensaje, data}`.
 */
$ctx = CargoMutationCtx::abrirEliminar($_POST);
if ($ctx === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
/** @var ActividadCargoEliminar $useCase */
$useCase = DependencyResolver::get(ActividadCargoEliminar::class);
$error_txt = $useCase->execute($ctx);
ContestarJson::enviar($error_txt, 'ok');
