<?php


/**
 * Endpoint JSON: actualiza el `nivel_stgr` de una persona.
 */

use src\personas\application\StgrUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom');
$Qid_tabla = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tabla');
$Qnivel_stgr = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'nivel_stgr');

/** @var StgrUpdate $useCase */
$useCase = DependencyResolver::get(StgrUpdate::class);
$error_txt = $useCase->execute($Qid_nom, $Qid_tabla, $Qnivel_stgr);

ContestarJson::enviar($error_txt, 'ok');
