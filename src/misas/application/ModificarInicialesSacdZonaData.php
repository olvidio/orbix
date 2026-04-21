<?php

namespace src\misas\application;

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ModificarInicialesSacdZonaData
{
    public static function getData(): array
    {
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);

        return [
            'a_opciones' => $ZonaRepository->getArrayZonas(),
        ];
    }
}
