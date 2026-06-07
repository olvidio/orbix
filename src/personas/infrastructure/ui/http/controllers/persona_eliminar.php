<?php

/**
 * Endpoint JSON: elimina una persona.
 */

use src\personas\application\PersonaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$Qid_nom = input_int($_POST, 'id_nom');
$Qobj_pau = input_string($_POST, 'obj_pau');

/** @var PersonaEliminar $useCase */
$useCase = DependencyResolver::get(PersonaEliminar::class);
$error_txt = $useCase->execute($Qid_nom, $Qobj_pau);

ContestarJson::enviar($error_txt, 'ok');
