<?php

namespace src\zonassacd\application;

use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ZonaSacdPage
{
    public static function getData(): array
    {
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        return [
            'a_opciones' => $ZonaRepository->getArrayZonas(),
            'perm_des' => (bool)(($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))),
        ];
    }
}
