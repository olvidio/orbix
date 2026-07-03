<?php


/**
 * Endpoint JSON: elimina una persona.
 */

use src\personas\application\PersonaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom');
$Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_pau');

/** @var PersonaEliminar $useCase */
$useCase = DependencyResolver::get(PersonaEliminar::class);
$error_txt = $useCase->execute($Qid_nom, $Qobj_pau);

ContestarJson::enviar($error_txt, 'ok');
