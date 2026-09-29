<?php

use src\actividades\application\TipoActivNuevo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_nuevo'),
        'tipo_activ_nuevo'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var TipoActivNuevo $useCase */
$useCase = DependencyResolver::get(TipoActivNuevo::class);
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
