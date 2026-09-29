<?php

/**
 * Endpoint JSON: aplica traslado de centro/delegacion.
 */

use src\personas\application\TrasladoUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_update'),
        'traslado_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_pau');
$_POST['obj_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau');

/** @var TrasladoUpdate $useCase */
$useCase = DependencyResolver::get(TrasladoUpdate::class);
$error_txt = $useCase->execute($_POST);

ContestarJson::enviar($error_txt, 'ok');
