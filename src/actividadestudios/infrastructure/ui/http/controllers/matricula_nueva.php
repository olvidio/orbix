<?php

use src\actividadestudios\application\MatriculaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_nueva'),
        'matricula_nueva'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
$_POST['id_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_pau');

/** @var MatriculaNueva $useCase */
$useCase = DependencyResolver::get(MatriculaNueva::class);
$result = $useCase->execute($_POST);
if ($result['requiere_confirmacion']) {
    ContestarJson::enviar('', [
        'requiere_confirmacion' => true,
        'mensaje' => $result['mensaje'],
    ]);
} else {
    ContestarJson::enviar($result['error'], 'ok');
}
