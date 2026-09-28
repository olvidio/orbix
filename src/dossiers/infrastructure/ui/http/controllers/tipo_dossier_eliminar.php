<?php

use src\dossiers\application\TipoDossierEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'tipo_dossier_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_tipo_dossier'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_dossier');

/** @var TipoDossierEliminar $useCase */
$useCase = DependencyResolver::get(TipoDossierEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
