<?php

namespace src\menus\application;


use src\menus\domain\contracts\GrupMenuRepositoryInterface;

class GrupMenuColeccionUseCase
{
    public function __invoke()
    {
        $GrupMenuRepository = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
        $cGrupMenus = $GrupMenuRepository->getGrupMenus(['_ordre' => 'orden']);

        return  $cGrupMenus;
    }
}
