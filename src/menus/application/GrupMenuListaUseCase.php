<?php

namespace src\menus\application;

use src\menus\application\repositories\GrupMenuRepository;

class ListaGrupMenus
{
    public function __invoke()
    {
        $GrupMenuRepository = new GrupMenuRepository();
        $cGrupMenus = $GrupMenuRepository->getGrupMenus(['_ordre' => 'orden']);

        $a_lista = [];
        $a_valores = [];
        $i = 0;
        foreach ($cGrupMenus as $oGrupMenu) {
            $a_lista[$oGrupMenu->getId_grupmenu()] = $oGrupMenu->getGrup_menu();
            $i++;
            $a_valores[$i]['sel'] = $oGrupMenu->getId_grupmenu() .'#';
            $a_valores[$i][1] = $oGrupMenu->getGrup_menu();
            $a_valores[$i][2] = $oGrupMenu->getOrden();
        }

        $data = [
            'a_lista' => $a_lista,
            'a_valores' => $a_valores,
        ];

        return $data;

    }
}
