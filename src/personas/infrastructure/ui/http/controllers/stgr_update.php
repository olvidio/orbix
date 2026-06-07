<?php

/**
 * Endpoint JSON: actualiza el `nivel_stgr` de una persona.
 */

use src\personas\application\StgrUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$Qid_nom = input_int($_POST, 'id_nom');
$Qid_tabla = input_string($_POST, 'id_tabla');
$Qnivel_stgr = input_string($_POST, 'nivel_stgr');

/** @var StgrUpdate $useCase */
$useCase = DependencyResolver::get(StgrUpdate::class);
$error_txt = $useCase->execute($Qid_nom, $Qid_tabla, $Qnivel_stgr);

ContestarJson::enviar($error_txt, 'ok');
