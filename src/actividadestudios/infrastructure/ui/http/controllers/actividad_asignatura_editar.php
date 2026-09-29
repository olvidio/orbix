<?php

use src\actividadestudios\application\ActividadAsignaturaEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_editar'),
        'actividad_asignatura_editar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
$_POST['id_asignatura'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_asignatura');

/** @var ActividadAsignaturaEditar $useCase */
$useCase = DependencyResolver::get(ActividadAsignaturaEditar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
