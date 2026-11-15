<?php

use src\asistentes\application\AsistenteGuardar;
use src\asistentes\application\AsistenteMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Crea, edita o mueve un `Asistente`.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var AsistenteGuardar $useCase */
$useCase = DependencyResolver::get(AsistenteGuardar::class);
// Solo con la cápsula que firma el formulario; fija la actividad y/o la persona.
$fijado = AsistenteMutationCtx::abrirGuardar($_POST);
if ($fijado === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$error_txt = $useCase->execute($_POST, $fijado);
ContestarJson::enviar($error_txt, 'ok');
