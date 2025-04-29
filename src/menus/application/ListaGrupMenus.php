<?php

namespace src\menus\application;

use src\menus\application\repositories\GrupMenuRepository;

class ListaGrupMenus
{
    public function __invoke()
    {
        $GrupMenuRepository = new GrupMenuRepository();
        $a_opciones = $GrupMenuRepository->getArrayGrupMenus();

        $data = [
            'a_opciones' => $a_opciones,
        ];

        return $data;

    }
}
