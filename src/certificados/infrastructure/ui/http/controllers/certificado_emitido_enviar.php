<?php

use src\certificados\domain\CertificadoEmitidoEnviar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/** @var CertificadoEmitidoEnviar $useCase */
$useCase = DependencyResolver::get(CertificadoEmitidoEnviar::class);

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_enviar'),
        'certificado_emitido_enviar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
// El certificado que se envía es el de la cápsula de la fila marcada en la lista.
$Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');

$error_txt = $useCase->execute($Qid_item);

ContestarJson::enviar($error_txt, 'ok');
