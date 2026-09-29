<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionUpdate;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'direccion_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['obj_dir'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_dir');
$input['id_ubi'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi');
$input['idx'] = (string)($ctx['idx'] ?? '');
$input['id_direccion'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_direccion');

$errorTxt = DependencyResolver::get(DireccionUpdate::class)->execute($input);
ContestarJson::enviar($errorTxt, ['ok' => true]);
