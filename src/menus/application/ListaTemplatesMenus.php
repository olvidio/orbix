<?php

namespace src\menus\application;

use src\menus\domain\contracts\TemplateMenuRepositoryInterface;

class ListaTemplatesMenus
{
    public function __construct(
        private TemplateMenuRepositoryInterface $templateMenuRepository,
    ) {
    }

    /** @return array{a_opciones: array<int|string, string>} */
    public function __invoke(): array
    {
        $a_opciones = $this->templateMenuRepository->getArrayTemplates();

        $data = [
            'a_opciones' => $a_opciones,
        ];

        return $data;

    }
}
