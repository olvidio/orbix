<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

final class TelecoDescLista
{
    public static function execute(int $id_tipo_teleco): array
    {
        $aOpciones = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class)->getArrayDescTelecoUbis($id_tipo_teleco);
        return ['a_desc' => $aOpciones];
    }
}
