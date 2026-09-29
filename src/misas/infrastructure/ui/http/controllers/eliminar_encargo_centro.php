<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\EliminarEncargoCentro;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'eliminar_encargo_centro'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_item');

/** @var EliminarEncargoCentro $useCase */
$useCase = DependencyResolver::get(EliminarEncargoCentro::class);
$result = $useCase->execute($Qid_item);

ContestarJson::enviar($result, ['id_item' => $Qid_item]);
