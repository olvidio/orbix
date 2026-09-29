<?php

use src\actividadestudios\application\MatriculaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$selIn = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
$ctxUnico = (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar');
$capsulas = $selIn !== [] ? $selIn : ($ctxUnico !== '' ? [$ctxUnico] : []);
if ($capsulas === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$selRebuilt = [];
try {
    foreach ($capsulas as $capsule) {
        $ctx = HashB::open($capsule, 'matricula_eliminar');
        $selToken = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'sel');
        if ($selToken === '') {
            ContestarJson::enviar(_("Operación no autorizada"), 'none');
            return;
        }
        $selRebuilt[] = $selToken;
        $pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'pau');
        if ($pau !== '') {
            $_POST['pau'] = $pau;
        }
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['sel'] = $selRebuilt;

/** @var MatriculaEliminar $useCase */
$useCase = DependencyResolver::get(MatriculaEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
