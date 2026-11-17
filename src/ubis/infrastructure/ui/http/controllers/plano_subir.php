<?php

/**
 * Sube (o reemplaza) el plano de una dirección: multipart con `userfile`. La dirección la fija
 * la cápsula `ctx_plano_subir` que firma `DireccionesEditarData` (ficha de la dirección).
 */

use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\ubis\application\PlanosDireccion;

MultipartUploadGuard::exitIfPostTooLargeJson();

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_plano_subir'),
        'plano_subir'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$uploaded = MultipartUploadGuard::requireUploadedFileOrExit('userfile');
$fichero = $uploaded['tmp_name'] !== '' ? file_get_contents($uploaded['tmp_name']) : false;
if (!is_string($fichero)) {
    ContestarJson::enviar(_("No se ha podido leer el archivo."), 'none');
    return;
}

$info = pathinfo($uploaded['name']);
try {
    DependencyResolver::get(PlanosDireccion::class)->subir(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_dir'),
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_direccion'),
        $info['filename'],
        $info['extension'] ?? '',
        $fichero
    );
} catch (InvalidArgumentException $e) {
    ContestarJson::enviar($e->getMessage(), 'none');
    return;
}

ContestarJson::enviar('', 'ok');
