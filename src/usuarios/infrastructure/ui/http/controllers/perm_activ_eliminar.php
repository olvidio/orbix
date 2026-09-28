<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'perm_activ_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = '';
$Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');

$PermUsuarioActividadRepository = DependencyResolver::get(PermUsuarioActividadRepositoryInterface::class);
$oPermUsuarioActividad = $PermUsuarioActividadRepository->findById($Qid_item);
if ($oPermUsuarioActividad === null) {
    $error_txt .= _("no existe el registro");
} else {
    if ($PermUsuarioActividadRepository->Eliminar($oPermUsuarioActividad) === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $PermUsuarioActividadRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');
