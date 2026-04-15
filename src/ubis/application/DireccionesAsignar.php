<?php

namespace src\ubis\application;

final class DireccionesAsignar
{
    public static function execute(int $id_ubi, string $obj_dir, int $id_direccion): array
    {
        $UbiRepository = DireccionesResolver::ubiRepo($obj_dir);
        $oUbi = $UbiRepository->findById($id_ubi);
        $oUbi->addDireccion($id_direccion, false, true);
        return ['ok' => true];
    }
}
