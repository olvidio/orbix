<?php

use src\dbextern\application\DesunirPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_desunir'),
        'sincro_desunir'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$id_nom_listas = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom_listas');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'tipo_persona');

$error_txt = DependencyResolver::get(DesunirPersonaUseCase::class)($id_nom_listas, $tipo_persona);

ContestarJson::enviar($error_txt, 'ok');
