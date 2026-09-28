<?php

/**
 * Endpoint JSON: guarda los datos de una persona.
 */

use src\personas\application\PersonaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_update'),
        'persona_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_nom'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');
$input['obj_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau');

/** @var PersonaUpdate $useCase */
$useCase = DependencyResolver::get(PersonaUpdate::class);
$error_txt = $useCase->execute($input);

ContestarJson::enviar($error_txt, 'ok');
