<?php

use src\actividades\application\TipoActivEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'tipo_activ_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_tipo_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_activ');

/** @var TipoActivEliminar $useCase */
$useCase = DependencyResolver::get(TipoActivEliminar::class);
$mensaje = $useCase->execute($input);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
