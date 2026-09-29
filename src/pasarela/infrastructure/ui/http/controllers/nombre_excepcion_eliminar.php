<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\pasarela\application\NombreExcepcionEliminar;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'nombre_excepcion_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_tipo_activ');

/** @var NombreExcepcionEliminar $useCase */
$useCase = DependencyResolver::get(NombreExcepcionEliminar::class);

$error_txt = $useCase->execute($id_tipo_activ);
ContestarJson::enviar($error_txt, 'ok');
