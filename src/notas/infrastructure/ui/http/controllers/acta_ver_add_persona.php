<?php

/**
 * Endpoint JSON: añade alumno con nota al dossier desde acta_ver.
 */

use src\notas\application\ActaVerAddPersona;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/** @var ActaVerAddPersona $useCase */
$useCase = DependencyResolver::get(ActaVerAddPersona::class);
try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_add_persona'),
        'acta_ver_add_persona'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$input = $_POST;
$input['acta'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'acta');
$result = $useCase->execute($input);

$error = ($result['success'] ?? false) ? '' : (string) ($result['mensaje'] ?? _("error"));
ContestarJson::enviar($error, [
    'mensaje' => (string) ($result['mensaje'] ?? ''),
]);
