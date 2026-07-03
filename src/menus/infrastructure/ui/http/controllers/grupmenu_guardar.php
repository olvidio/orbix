<?php

use src\shared\infrastructure\DependencyResolver;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\entity\GrupMenu;
use src\shared\web\ContestarJson;

$Qgrupmenu = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'grupmenu');
$Qid_grupmenu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_grupmenu');
$Qorden = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'orden');

$error_txt = '';

if ($Qgrupmenu) {
    /** @var GrupMenuRepositoryInterface $GrupMenuRepository */
    /** @var GrupMenuRepositoryInterface $GrupMenuRepository */
$GrupMenuRepository = DependencyResolver::get(GrupMenuRepositoryInterface::class);
    if (!empty($Qid_grupmenu)) {
        $oGrupMenu = $GrupMenuRepository->findById($Qid_grupmenu);
        if ($oGrupMenu === null) {
            ContestarJson::enviar(_("No encuentro el grupmenu"), 'ok');
            return;
        }
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