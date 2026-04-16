<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class CentrosFormLaborData
{
    public static function execute(int $id_ubi): array
    {
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = $CentroDlRepository->findById($id_ubi);

        return [
            'id_ubi' => $id_ubi,
            'nombre_ubi' => $oCentro?->getNombre_ubi() ?? '',
            'tipo_ctr' => $oCentro?->getTipo_ctr() ?? '',
            'tipo_labor' => $oCentro?->getTipo_labor() ?? 0,
        ];
    }
}

