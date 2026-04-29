<?php

use src\asistentes\application\services\AsistenteActividadService;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\ubiscamas\domain\value_objects\CamaId;

$ctxRaw = (string)filter_input(INPUT_POST, 'ctx');
try {
    $opened = HashB::open($ctxRaw, 'update_cama_asistente');
} catch (HashBInvalidException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_activ = (int)($opened['id_activ'] ?? 0);
if ($Qid_activ <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => _('Operación no autorizada')]);
    exit;
}

$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qid_cama = (string)filter_input(INPUT_POST, 'id_cama'); // can be string (uuid)

$jsondata = [];

try {
    $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);

    // find the specified attendance instance
    $oAsistente = $AsistenteActividadService->buscarAsistencia($Qid_nom, $Qid_activ);
    if ($oAsistente === FALSE) {
        throw new \Exception("Asistencia no encontrada para id_nom $Qid_nom e id_activ $Qid_activ.");
    }

    $repoName = $AsistenteActividadService->getRepoAsistente($Qid_nom, $Qid_activ);
    $AsistenteRepository = $GLOBALS['container']->get($repoName);

    // Assign bed or unassign if empty
    $uuid_cama = CamaId::fromNullableString($Qid_cama);
    $oAsistente->setCamaVo($uuid_cama ?: null);

    if ($AsistenteRepository->Guardar($oAsistente) === FALSE) {
        throw new \Exception("Error al guardar la asignación de la cama.");
    }

    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'ok';
} catch (\Exception $e) {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($jsondata);
