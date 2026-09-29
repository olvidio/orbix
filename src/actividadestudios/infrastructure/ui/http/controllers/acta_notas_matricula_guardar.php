<?php

use src\actividadestudios\application\ActaNotasMatriculaGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'acta_notas_matricula_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
$_POST['id_asignatura'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_asignatura');
$_POST['id_schema'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_schema');

/** @var ActaNotasMatriculaGuardar $useCase */
$useCase = DependencyResolver::get(ActaNotasMatriculaGuardar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
