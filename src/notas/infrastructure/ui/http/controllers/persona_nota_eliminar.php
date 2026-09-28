<?php

use src\notas\application\PersonaNotaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

/**
 * Elimina una `PersonaNota`. Responde JSON `{success, mensaje, data}`.
 */
try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'persona_nota_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['sel'] = [];
$input['id_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');
$input['id_nivel'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nivel');
$input['id_asignatura'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_asignatura');
$input['tipo_acta'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'tipo_acta');

$error_txt = (DependencyResolver::get(PersonaNotaEliminar::class))->execute($input);
ContestarJson::enviar($error_txt, 'ok');
