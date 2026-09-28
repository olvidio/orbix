<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisGuardar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'ubis_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['obj_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau');
$input['id_ubi'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi');

/** @var UbisGuardar $useCase */
$useCase = DependencyResolver::get(UbisGuardar::class);
try {
    $errorTxt = $useCase->execute($input);
} catch (\Throwable $e) {
    $errorTxt = $e->getMessage();
}
ContestarJson::enviar($errorTxt, 'ok');
