<?php

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

$ctxRaw = (string)filter_input(INPUT_POST, 'ctx');
try {
    $opened = HashB::open($ctxRaw, 'update_solo_vip');
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

$Qsolo_vip = (string)filter_input(INPUT_POST, 'solo_vip'); // 'true' or 'false' as string from JS

$jsondata = [];

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

if (ob_get_length()) {
    ob_start();
    ob_clean();
}
header('Content-Type: application/json');
echo json_encode($jsondata);
