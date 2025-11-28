<?php

namespace src\menus\application;


use src\menus\domain\contracts\MetaMenuRepositoryInterface;

class ListaMetaMenus
{
    public function __invoke()
    {
        $MetaMenuRepository = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);
        $a_opciones = $MetaMenuRepository->getArrayMetaMenus();

        $data = [
            'a_opciones' => $a_opciones,
        ];

        return $data;

    }
}
