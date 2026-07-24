<?php

/**
 * Endpoint JSON: publica una persona para DL destino (caso B).
 */

use src\personas\application\PersonaPublicar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom');
$Qid_schema = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_schema');
$Qdl = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl');
$aDl = $_POST['dl'] ?? $Qdl;
if (!is_array($aDl)) {
    $aDl = $Qdl !== '' ? [$Qdl] : [];
}

/** @var PersonaPublicar $useCase */
$useCase = DependencyResolver::get(PersonaPublicar::class);
$error_txt = $useCase->execute($Qid_nom, $Qid_schema, $aDl);

ContestarJson::enviar($error_txt, 'ok');
