<?php

namespace src\menus\application;

use src\menus\application\repositories\MetaMenuRepository;

class ListaMetaMenus
{
    public function __invoke()
    {
        $MetaMenuRepository = new MetaMenuRepository();
        $a_opciones = $MetaMenuRepository->getArrayMetaMenus();

        $data = [
            'a_opciones' => $a_opciones,
        ];

        return $data;

    }
}
