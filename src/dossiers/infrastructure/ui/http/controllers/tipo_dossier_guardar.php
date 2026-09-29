<?php

use src\dossiers\application\TipoDossierGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'tipo_dossier_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_tipo_dossier'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_dossier');

/** @var TipoDossierGuardar $useCase */
$useCase = DependencyResolver::get(TipoDossierGuardar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
