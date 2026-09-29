<?php

use src\actividades\application\TipoActivUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'tipo_activ_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_tipo_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_activ');

/** @var TipoActivUpdate $useCase */
$useCase = DependencyResolver::get(TipoActivUpdate::class);
$mensaje = $useCase->execute($input);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
