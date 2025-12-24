<?php

namespace src\menus\application;


use src\menus\domain\contracts\MenuDbRepositoryInterface;

class MenuEliminar
{
    public function __invoke(int $id_menu): string
    {
        $error_txt = '';

        $MenuDbRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);
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