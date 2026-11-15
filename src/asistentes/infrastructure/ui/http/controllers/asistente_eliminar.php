<?php

use src\asistentes\application\AsistenteEliminar;
use src\asistentes\application\AsistenteMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Elimina un `Asistente` y sus matriculas.
 * Responde JSON `{success, mensaje, data}`.
 */
/** @var AsistenteEliminar $useCase */
$useCase = DependencyResolver::get(AsistenteEliminar::class);
// Solo con la cápsula de la fila marcada: la asistencia que se borra sale de ella, no del POST.
$ids = AsistenteMutationCtx::abrirEliminar($_POST);
if ($ids === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$error_txt = $useCase->execute(['pau' => 'p', 'sel' => [], 'id_activ' => $ids['id_activ'], 'id_nom' => $ids['id_nom']]);
ContestarJson::enviar($error_txt, 'ok');
