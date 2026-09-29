<?php

use src\actividadestudios\application\AsistentePlanEstOk;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_plan_est_ok'),
        'asistente_plan_est_ok'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
$_POST['id_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_pau');

/** @var AsistentePlanEstOk $useCase */
$useCase = DependencyResolver::get(AsistentePlanEstOk::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
