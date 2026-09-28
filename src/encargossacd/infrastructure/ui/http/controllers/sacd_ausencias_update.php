<?php

use src\encargossacd\application\SacdAusenciasUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'sacd_ausencias_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_nom'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');

/** @var SacdAusenciasUpdate $useCase */
$useCase = DependencyResolver::get(SacdAusenciasUpdate::class);

$resultado = $useCase->execute($input);

ContestarJson::enviar(
    (string)$resultado['error'],
    (string)$resultado['mensajes'],
);
