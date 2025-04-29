<?php

namespace src\menus\application;

use src\menus\application\repositories\MenuDbRepository;
use src\menus\domain\entity\MenuDb;

class MenuCopiar
{
    public function __invoke(int $id_menu, string $gm_new): string
    {
        $error_txt = '';

        $MenuDbRepository = new MenuDbRepository();
        $oMenuDb = $MenuDbRepository->findById($id_menu);

        if (empty($oMenuDb)) {
            $error_txt = _("No encuentro el menu");
        } else {
            // Clonar y poner en otro grupmenu
            $ok = $oMenuDb->isOk();
            $orden = $oMenuDb->getOrden();
            $id_grupmenu = $oMenuDb->getId_grupmenu();
            $txt_menu = $oMenuDb->getMenu();
            $parametros = $oMenuDb->getParametros();
            $id_metamenu = $oMenuDb->getId_metamenu();
            $perm_menu = $oMenuDb->getMenu_perm();

            $id_menu_new = $MenuDbRepository->getNewId();
            $oNewMenuDb = new MenuDb();
            $oNewMenuDb->setId_menu($id_menu_new);
            $oNewMenuDb->setOk($ok);
            $oNewMenuDb->setOrden($orden);
            $oNewMenuDb->setId_grupmenu($id_grupmenu);
            $oNewMenuDb->setMenu($txt_menu);
            $oNewMenuDb->setParametros($parametros);
            $oNewMenuDb->setId_metamenu($id_metamenu);
            $oNewMenuDb->setMenu_perm($perm_menu);
            $oNewMenuDb->setId_grupmenu($gm_new);
            if ($MenuDbRepository->Guardar($oNewMenuDb) === false) {
                $error_txt .= _("hay un error, no se ha guardado");
                $error_txt .= "\n" . $MenuDbRepository->getErrorTxt();
            }
        }
        return $error_txt;
    }
}