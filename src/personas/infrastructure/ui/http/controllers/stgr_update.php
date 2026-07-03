<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint JSON: actualiza el `nivel_stgr` de una persona.
 */

use src\personas\application\StgrUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_nom = FuncTablasSupport::inputInt($_POST, 'id_nom');
$Qid_tabla = FuncTablasSupport::inputString($_POST, 'id_tabla');
$Qnivel_stgr = FuncTablasSupport::inputString($_POST, 'nivel_stgr');

/** @var StgrUpdate $useCase */
$useCase = DependencyResolver::get(StgrUpdate::class);
$error_txt = $useCase->execute($Qid_nom, $Qid_tabla, $Qnivel_stgr);

ContestarJson::enviar($error_txt, 'ok');
