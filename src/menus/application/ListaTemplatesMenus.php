<?php

namespace src\menus\application;


use src\menus\domain\contracts\TemplateMenuRepositoryInterface;

class ListaTemplatesMenus
{
    public function __invoke()
    {
        $TemplateMenuRepository = $GLOBALS['container']->get(TemplateMenuRepositoryInterface::class);
        $a_opciones = $TemplateMenuRepository->getArrayTemplates();

        $data = [
            'a_opciones' => $a_opciones,
        ];

        return $data;

    }
}
