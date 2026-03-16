<?php

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\entity\GrupMenu;
use web\ContestarJson;

$Qgrupmenu = (string)filter_input(INPUT_POST, 'grupmenu');
$Qid_grupmenu = (integer)filter_input(INPUT_POST, 'id_grupmenu');
$Qorden = (integer)filter_input(INPUT_POST, 'orden');

$error_txt = '';

if ($Qgrupmenu) {
    $GrupMenuRepository = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
    if (!empty($Qid_grupmenu)) {
        $oGrupMenu = $GrupMenuRepository->findById($Qid_grupmenu);
    } else {
        $id_grupmenu_new = $GrupMenuRepository->getNewId();
        $oGrupMenu = new GrupMenu();
        $oGrupMenu->setId_grupmenu($id_grupmenu_new);
    }
    $oGrupMenu->setGrup_menu($Qgrupmenu);
    $oGrupMenu->setOrden($Qorden);
    if ($GrupMenuRepository->Guardar($oGrupMenu) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $GrupMenuRepository->getErrorTxt();
    }
} else {
    $error_txt = _("debe poner un nombre");
}

ContestarJson::enviar($error_txt, 'ok');