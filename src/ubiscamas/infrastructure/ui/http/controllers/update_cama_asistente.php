<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\ubiscamas\application\UpdateCamaAsistente;
use src\shared\domain\helpers\FuncTablasSupport;
$ctxRaw = FuncTablasSupport::inputString($_POST, 'ctx');
try {
    $opened = HashB::open($ctxRaw, 'update_cama_asistente');
} catch (HashBInvalidException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_activ = FuncTablasSupport::inputInt($opened, 'id_activ');
if ($Qid_activ <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_nom = FuncTablasSupport::inputInt($_POST, 'id_nom');
$Qid_cama = FuncTablasSupport::inputString($_POST, 'id_cama');

/** @var UpdateCamaAsistente $useCase */
$useCase = DependencyResolver::get(UpdateCamaAsistente::class);
$jsondata = $useCase->execute($Qid_nom, $Qid_activ, $Qid_cama);

header('Content-Type: application/json');
echo json_encode($jsondata);
