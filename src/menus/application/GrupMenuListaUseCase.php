<?php

namespace src\menus\application;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;

class GrupMenuListaUseCase
{
    public function __construct(
        private GrupMenuRepositoryInterface $grupMenuRepository,
    ) {
    }

    /** @return array{a_lista: array<int, string>, a_valores: array<int, array{sel: string, 1: string, 2: int|null}>} */
    public function __invoke(): array
    {
        $cGrupMenus = $this->grupMenuRepository->getGrupMenus(['_ordre' => 'orden']);

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
