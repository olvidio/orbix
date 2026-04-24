<?php

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$Qsolo_vip = (string)filter_input(INPUT_POST, 'solo_vip'); // 'true' or 'false' as string from JS

$jsondata = [];
$error_txt = '';

try {
    $ActividadRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    $oActividad = $ActividadRepository->findById($Qid_activ);
    
    if ($oActividad === null) {
        throw new \Exception("Actividad no encontrada con id $Qid_activ.");
    }
    
    $desc_activ = ($Qsolo_vip === 'true') ? 'camasVIP' : '';
    $oActividad->setDesc_activ($desc_activ);
    
    if ($ActividadRepository->Guardar($oActividad) === FALSE) {
        throw new \Exception("Error al guardar el estado VIP de la actividad: " . $ActividadRepository->getErrorTxt());
    }

    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'ok';
} catch (\Exception $e) {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $e->getMessage();
}

// return json response
if (ob_get_length()) {
    ob_start();
    ob_clean();
}
header('Content-Type: application/json');
echo json_encode($jsondata);
