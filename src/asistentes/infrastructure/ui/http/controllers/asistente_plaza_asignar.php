<?php

use src\asistentes\application\AsistenteMutationCtx;
use src\asistentes\application\AsistentePlazaAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Cambia la plaza de un lote de asistentes.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var AsistentePlazaAsignar $useCase */
$useCase = DependencyResolver::get(AsistentePlazaAsignar::class);
// Solo con la cápsula de la lista de asistentes de la actividad, que fija la actividad.
$idActiv = AsistenteMutationCtx::abrirPlazaAsignar($_POST);
if ($idActiv === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$input = $_POST;
$input['id_activ'] = $idActiv;
$error_txt = $useCase->execute($input);
ContestarJson::enviar($error_txt, 'ok');
