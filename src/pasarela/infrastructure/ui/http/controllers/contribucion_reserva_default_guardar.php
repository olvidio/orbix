<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaDefaultGuardar;

try {
    HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'contribucion_reserva_default_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$default = (string)\src\shared\domain\helpers\FilterPostGet::post('default');

/** @var ContribucionReservaDefaultGuardar $useCase */
$useCase = DependencyResolver::get(ContribucionReservaDefaultGuardar::class);

$error_txt = $useCase->execute($default);
ContestarJson::enviar($error_txt, 'ok');
