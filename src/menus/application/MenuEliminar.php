<?php

namespace src\menus\application;

use src\menus\domain\contracts\MenuDbRepositoryInterface;

class MenuEliminar
{
    public function __construct(
        private MenuDbRepositoryInterface $menuDbRepository,
    ) {
    }

    public function __invoke(int $id_menu): string
    {
        $error_txt = '';

        $oMenuDb = $this->menuDbRepository->findById($id_menu);
        if (empty($oMenuDb)) {
            $error_txt = _("No encuentro el menu");
        } else {
            if ($this->menuDbRepository->Eliminar($oMenuDb) === false) {
                $error_txt .= _("hay un error, no se ha eliminado");
                $error_txt .= "\n" . $this->menuDbRepository->getErrorTxt();
            }
        }
        return $error_txt;
    }
}