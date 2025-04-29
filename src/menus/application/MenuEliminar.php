<?php

namespace src\menus\application;

use src\menus\application\repositories\MenuDbRepository;

class MenuEliminar
{
    public function __invoke(int $id_menu): string
    {
        $error_txt = '';

        $MenuDbRepository = new MenuDbRepository();
        $oMenuDb = $MenuDbRepository->findById($id_menu);
        if (empty($oMenuDb)) {
            $error_txt = _("No encuentro el menu");
        } else {
            if ($MenuDbRepository->Eliminar($oMenuDb) === false) {
                $error_txt .= _("hay un error, no se ha eliminado");
                $error_txt .= "\n" . $MenuDbRepository->getErrorTxt();
            }
        }
        return $error_txt;
    }
}