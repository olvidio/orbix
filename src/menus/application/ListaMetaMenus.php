<?php

namespace src\menus\application;

use src\menus\domain\contracts\MetaMenuRepositoryInterface;

class ListaMetaMenus
{
    public function __construct(
        private MetaMenuRepositoryInterface $metaMenuRepository,
    ) {
    }

    /** @return array{a_opciones: array<int|string, string>} */
    public function __invoke(): array
    {
        $a_opciones = $this->metaMenuRepository->getArrayMetaMenus();

        $data = [
            'a_opciones' => $a_opciones,
        ];

        return $data;

    }
}
