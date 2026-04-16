<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class CentrosFormNumData
{
    public static function execute(int $id_ubi): array
    {
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = $CentroDlRepository->findById($id_ubi);

        return [
            'id_ubi' => $id_ubi,
            'nombre_ubi' => $oCentro?->getNombre_ubi() ?? '',
            'n_buzon' => $oCentro?->getN_buzon() ?? '',
            'num_pi' => $oCentro?->getNum_pi() ?? '',
            'num_cartas' => $oCentro?->getNum_cartas() ?? '',
        ];
    }
}

