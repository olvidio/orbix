<?php

use src\notas\application\PersonaNotaEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/**
 * Edita una `PersonaNota` existente. Responde JSON `{success, mensaje?, data}`.
 * En éxito, `data` incluye mensaje con el esquema de escritura.
 */
try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'persona_nota_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$input = $_POST;
$input['id_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_pau');
$r = (DependencyResolver::get(PersonaNotaEditar::class))->execute($input);
ContestarJson::enviar($r['error'], [
    'mensaje' => $r['mensaje'],
    'esquema' => $r['esquema'],
]);
