<?php

namespace src\ubis\application;

final class DireccionesQuitar
{
    public static function execute(int $id_ubi, int $idx, string $obj_dir, string $id_direccion_csv): array
    {
        $a_id_direccion = explode(',', $id_direccion_csv);
        $id_direccion = (int)($a_id_direccion[$idx] ?? 0);
        $UbiRepository = DireccionesResolver::ubiRepo($obj_dir);
        $oUbi = $UbiRepository->findById($id_ubi);
        $oUbi->removeDireccion($id_direccion);
        return ['ok' => true];
    }
}
