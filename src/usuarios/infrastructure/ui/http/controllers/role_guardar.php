<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use src\usuarios\domain\value_objects\RoleName;
use src\shared\web\ContestarJson;

$Qrole = (string)\src\shared\domain\helpers\FilterPostGet::post('role');
$Qid_role = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_role');
$Qsf = (integer)\src\shared\domain\helpers\FilterPostGet::post('sf');
$Qsv = (integer)\src\shared\domain\helpers\FilterPostGet::post('sv');
$Qpau = (string)\src\shared\domain\helpers\FilterPostGet::post('pau');
$Qdmz = (integer)\src\shared\domain\helpers\FilterPostGet::post('dmz');

$error_txt = '';

if ($Qrole) {
    $RoleRepository = DependencyResolver::get(RoleRepositoryInterface::class);
    if (!empty($Qid_role)) {
        $oRole = $RoleRepository->findById($Qid_role);
        if ($oRole === null) {
            ContestarJson::enviar(_('Rol no encontrado'), 'none');
            return;
        }
    } else {
        $id_role_new = $RoleRepository->getNewId();
        $oRole = new Role();
        $oRole->setId_role($id_role_new);
    }
    $oRole->setRoleVo(new RoleName($Qrole));
    $oRole->setSf(!empty($Qsf));
    $oRole->setSv(!empty($Qsv));
    $pauStr = $Qpau === '' ? PauType::PAU_NONE : $Qpau;
    $oRole->setPauVo(new PauType($pauStr));
    $oRole->setDmz(!empty($Qdmz));
    if ($RoleRepository->Guardar($oRole) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $RoleRepository->getErrorTxt();
    }
} else {
    $error_txt = _("debe poner un nombre");
}

ContestarJson::enviar($error_txt, 'ok');