<?php

use src\asistentes\application\services\AsistenteActividadService;
use src\ubiscamas\domain\value_objects\CamaId;

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qid_cama = (string)filter_input(INPUT_POST, 'id_cama'); // can be string (uuid)

$error_txt = '';

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

// return json response
header('Content-Type: application/json');
echo json_encode($jsondata);
