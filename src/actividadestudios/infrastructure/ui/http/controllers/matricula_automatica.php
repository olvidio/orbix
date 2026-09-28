<?php

use src\actividadestudios\application\MatriculaAutomatica;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_auto'),
        'matricula_automatica'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

if (array_key_exists('id_pau', $ctx)) {
    $_POST['id_pau'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_pau');
}
if (array_key_exists('id_activ', $ctx)) {
    $_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
}
unset($_POST['sel']);

/** @var MatriculaAutomatica $useCase */
$useCase = DependencyResolver::get(MatriculaAutomatica::class);
$result = $useCase->execute($_POST);
if ($result['success']) {
    ContestarJson::enviar('', ['msg' => $result['msg']]);
} else {
    ContestarJson::enviar($result['msg'], 'ok');
}
