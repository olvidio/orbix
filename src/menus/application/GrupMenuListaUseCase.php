<?php

namespace src\menus\application;

use src\menus\application\repositories\GrupMenuRepository;

class GrupMenuListaUseCase
{
    public function __invoke()
    {
        $GrupMenuRepository = new GrupMenuRepository();
        $a_opciones = $GrupMenuRepository->getArrayGrupMenus();

        return [
            'a_opciones' => $a_opciones,
        ];
    }
}
