<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\ubiscamas\application\UpdateCamaAsistente;
$ctxRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx');
try {
    $opened = HashB::open($ctxRaw, 'update_cama_asistente');
} catch (HashBInvalidException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($opened, 'id_activ');
if ($Qid_activ <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom');
$Qid_cama = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_cama');

/** @var UpdateCamaAsistente $useCase */
$useCase = DependencyResolver::get(UpdateCamaAsistente::class);
$jsondata = $useCase->execute($Qid_nom, $Qid_activ, $Qid_cama);

header('Content-Type: application/json');
echo json_encode($jsondata);
