<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\ubiscamas\application\UpdateCamaAsistente;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$ctxRaw = input_string($_POST, 'ctx');
try {
    $opened = HashB::open($ctxRaw, 'update_cama_asistente');
} catch (HashBInvalidException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_activ = input_int($opened, 'id_activ');
if ($Qid_activ <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_nom = input_int($_POST, 'id_nom');
$Qid_cama = input_string($_POST, 'id_cama');

/** @var UpdateCamaAsistente $useCase */
$useCase = DependencyResolver::get(UpdateCamaAsistente::class);
$jsondata = $useCase->execute($Qid_nom, $Qid_activ, $Qid_cama);

header('Content-Type: application/json');
echo json_encode($jsondata);
