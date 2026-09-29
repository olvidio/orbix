<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\GuardarEncargoCentro;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'guardar_encargo_centro'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_item');
$Qid_enc = (int)\src\shared\domain\helpers\FilterPostGet::post('id_enc', FILTER_VALIDATE_INT);
$Qid_ctr = (int)\src\shared\domain\helpers\FilterPostGet::post('id_ctr', FILTER_VALIDATE_INT);

/** @var GuardarEncargoCentro $useCase */
$useCase = DependencyResolver::get(GuardarEncargoCentro::class);
$result = $useCase->execute($Qid_item, $Qid_enc, $Qid_ctr);

ContestarJson::enviar($result, [
    'id_item' => $Qid_item,
    'id_enc' => $Qid_enc,
    'id_ctr' => $Qid_ctr,
]);
