<?php

namespace src\menus\application;

use src\menus\application\repositories\GrupMenuRepository;

class GrupMenuColeccionUseCase
{
    public function __invoke()
    {
        $GrupMenuRepository = new GrupMenuRepository();
        $cGrupMenus = $GrupMenuRepository->getGrupMenus(['_ordre' => 'orden']);

        return  $cGrupMenus;
    }
}
