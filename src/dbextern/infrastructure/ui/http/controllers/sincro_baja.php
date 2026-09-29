<?php

use src\dbextern\application\BajaPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_baja'),
        'sincro_baja'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$dl = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'dl');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'tipo_persona');
$id_nom_orbix = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom_orbix');

$error_txt = DependencyResolver::get(BajaPersonaUseCase::class)($id_nom_orbix, $tipo_persona, $dl);

ContestarJson::enviar($error_txt, 'ok');
