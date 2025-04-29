<?php

namespace src\menus\application;

use src\menus\application\repositories\TemplateMenuRepository;

class ListaTemplatesMenus
{
    public function __invoke()
    {
        $TemplateMenuRepository = new TemplateMenuRepository();
        $a_opciones = $TemplateMenuRepository->getArrayTemplates();

        $data = [
            'a_opciones' => $a_opciones,
        ];

        return $data;

    }
}
