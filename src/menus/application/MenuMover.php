<?php

namespace src\menus\application;


use src\menus\domain\contracts\MenuDbRepositoryInterface;

class MenuMover
{
    public function __invoke(int $id_menu, string $gm_new): string
    {
        $error_txt = '';

        $MenuDbRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);
        $oMenuDb = $MenuDbRepository->findById($id_menu);

        if (empty($oMenuDb)) {
            $error_txt = _("No encuentro el menu");
        } else {
            $oMenuDb->setId_grupmenu($gm_new);
            if ($MenuDbRepository->Guardar($oMenuDb) === false) {
                $error_txt .= _("hay un error, no se ha guardado");
                $error_txt .= "\n" . $MenuDbRepository->getErrorTxt();
            }
        }
        return $error_txt;
    }
}