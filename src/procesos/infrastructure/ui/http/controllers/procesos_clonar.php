<?php

use src\procesos\application\ProcesosClonar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_clonar'),
        'procesos_clonar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_tipo_proceso'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_proceso');

/** @var ProcesosClonar $useCase */
$useCase = DependencyResolver::get(ProcesosClonar::class);

ContestarJson::enviar($useCase->execute($input));
