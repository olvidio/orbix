<?php

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
$ctxRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx');
try {
    $opened = HashB::open($ctxRaw, 'update_solo_vip');
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

$Qsolo_vip = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'solo_vip');

$jsondata = [];

try {
    /** @var ActividadAllRepositoryInterface $actividadRepository */
    $actividadRepository = DependencyResolver::get(ActividadAllRepositoryInterface::class);
    $oActividad = $actividadRepository->findById($Qid_activ);

    if ($oActividad === null) {
        throw new \Exception("Actividad no encontrada con id $Qid_activ.");
    }

    $desc_activ = ($Qsolo_vip === 'true') ? 'camasVIP' : '';
    $oActividad->setDesc_activ($desc_activ);

    if ($actividadRepository->Guardar($oActividad) === false) {
        throw new \Exception('Error al guardar el estado VIP de la actividad: ' . $actividadRepository->getErrorTxt());
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
