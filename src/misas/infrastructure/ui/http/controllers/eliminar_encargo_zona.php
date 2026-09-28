<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\EliminarEncargoZona;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'eliminar_encargo_zona'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_enc = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_enc');

/** @var EliminarEncargoZona $useCase */
$useCase = DependencyResolver::get(EliminarEncargoZona::class);
$result = $useCase->execute($Qid_enc);

ContestarJson::enviar($result, ['id_enc' => $Qid_enc]);
