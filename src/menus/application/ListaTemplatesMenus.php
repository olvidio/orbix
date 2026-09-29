<?php

namespace src\menus\application;

use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\shared\security\HashB;

class ListaTemplatesMenus
{
    public function __construct(
        private TemplateMenuRepositoryInterface $templateMenuRepository,
    ) {
    }

    /** @return array{a_opciones: array<int|string, string>, ctx_importar: string} */
    public function __invoke(): array
    {
        $a_opciones = $this->templateMenuRepository->getArrayTemplates();

        return [
            'a_opciones' => $a_opciones,
            'ctx_importar' => HashB::sign('menus_importar'),
        ];
    }
}
