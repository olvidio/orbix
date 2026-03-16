<?php

use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\entity\GrupMenuRole;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) { //vengo de un checkbox
    $GrupMenuRoleRepository = $GLOBALS['container']->get(GrupMenuRoleRepositoryInterface::class);
    foreach ($a_sel as $sel) {
        //$id_nom=$sel[0];
        $id_role = strtok($sel, "#");
        $id_grupmenu = strtok("#");
        $cGrupMenuRoles = $GrupMenuRoleRepository->getGrupMenuRoles(['id_role' => $id_role, 'id_grupmenu' => $id_grupmenu]);
        if (empty($cGrupMenuRoles)) {
            $id_item = $GrupMenuRoleRepository->getNewId();
            $oGrupMenuRole = new GrupMenuRole();
            $oGrupMenuRole->setId_item($id_item);
            $oGrupMenuRole->setId_role($id_role);
            $oGrupMenuRole->setId_grupmenu($id_grupmenu);
        } else {
            $oGrupMenuRole = $cGrupMenuRoles[0];
        }

        if ($GrupMenuRoleRepository->Guardar($oGrupMenuRole) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $GrupMenuRoleRepository->getErrorTxt();
        }
    }
} else {
    $error_txt = _("debe seleccionar uno");
}

ContestarJson::enviar($error_txt, 'ok');