<?php


/**
 * Endpoint JSON: elimina una persona.
 */

use src\personas\application\PersonaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'persona_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');
$Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau');

/** @var PersonaEliminar $useCase */
$useCase = DependencyResolver::get(PersonaEliminar::class);
$error_txt = $useCase->execute($Qid_nom, $Qobj_pau);

ContestarJson::enviar($error_txt, 'ok');
