<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'habitacion_delete'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_habitacion = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_habitacion');

/** @var HabitacionDlRepositoryInterface $habitacionRepository */
$habitacionRepository = DependencyResolver::get(HabitacionDlRepositoryInterface::class);

$error_txt = '';
try {
    $oHabitacion = $habitacionRepository->findById($Qid_habitacion);
    if ($oHabitacion === null) {
        $error_txt = _("No se encontró la habitación a eliminar");
    } elseif ($habitacionRepository->Eliminar($oHabitacion) === false) {
        $error_txt = _("hay un error, no se ha eliminado la habitación");
        $error_txt .= "\n" . $habitacionRepository->getErrorTxt();
    }
} catch (Exception $e) {
    $error_txt = _("Error al eliminar la habitación") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
