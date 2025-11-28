<?php

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_grupmenu = (integer)strtok($a_sel[0], "#");
}
$GrupMenuRepository = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
$oGrupMenu = $GrupMenuRepository->findById($id_grupmenu);
if ($GrupMenuRepository->Eliminar($oGrupMenu) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $GrupMenuRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');