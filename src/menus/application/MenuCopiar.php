<?php

namespace src\menus\application;

use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;

class MenuCopiar
{
    public function __construct(
        private MenuDbRepositoryInterface $menuDbRepository,
    ) {
    }

    public function __invoke(int $id_menu, string $gm_new): string
    {
        $error_txt = '';

        $oMenuDb = $this->menuDbRepository->findById($id_menu);

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

            $id_menu_new = $this->menuDbRepository->getNewId();
            $oNewMenuDb = new MenuDb();
            $oNewMenuDb->setId_menu($id_menu_new);
            $oNewMenuDb->setOk($ok);
            $oNewMenuDb->setOrden($orden);
            $oNewMenuDb->setId_grupmenu($id_grupmenu);
            $oNewMenuDb->setMenu($txt_menu);
            $oNewMenuDb->setParametros($parametros);
            $oNewMenuDb->setId_metamenu($id_metamenu);
            $oNewMenuDb->setMenu_perm($perm_menu);
            $oNewMenuDb->setId_grupmenu((int)$gm_new);
            if ($this->menuDbRepository->Guardar($oNewMenuDb) === false) {
                $error_txt .= _("hay un error, no se ha guardado");
                $error_txt .= "\n" . $this->menuDbRepository->getErrorTxt();
            }
        }
        return $error_txt;
    }
}