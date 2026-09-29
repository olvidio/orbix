<?php

use src\certificados\domain\CertificadoRecibidoDelete;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'certificado_recibido_delete'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var CertificadoRecibidoDelete $useCase */
$useCase = DependencyResolver::get(CertificadoRecibidoDelete::class);
$error_txt = $useCase->delete(\src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item'));

ContestarJson::enviar($error_txt, 'ok');
