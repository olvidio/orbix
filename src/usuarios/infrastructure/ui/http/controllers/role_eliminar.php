<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'role_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = '';
$id_role = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_role');

$RoleRepository = DependencyResolver::get(RoleRepositoryInterface::class);
$oRole = $RoleRepository->findById($id_role);
if ($oRole === null) {
    $error_txt .= _("no existe el registro");
} elseif ($RoleRepository->Eliminar($oRole) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $RoleRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
