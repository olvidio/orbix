<?php

namespace src\ubis\application;

use src\ubis\domain\entity\Ubi;

final class DireccionesQueData
{
    public static function execute(int $id_ubi): array
    {
        $oUbi = Ubi::newUbi($id_ubi);
        return [
            'tipo_ubi' => $oUbi->getTipo_ubi(),
            'titulo' => ucfirst(_("introduzca un valor para buscar una dirección existente")),
        ];
    }
}
