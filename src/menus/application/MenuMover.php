<?php

namespace src\menus\application;

use src\menus\domain\contracts\MenuDbRepositoryInterface;

class MenuMover
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
            $oMenuDb->setId_grupmenu((int)$gm_new);
            if ($this->menuDbRepository->Guardar($oMenuDb) === false) {
                $error_txt .= _("hay un error, no se ha guardado");
                $error_txt .= "\n" . $this->menuDbRepository->getErrorTxt();
            }
        }
        return $error_txt;
    }
}