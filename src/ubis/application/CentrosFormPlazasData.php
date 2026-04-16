<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class CentrosFormPlazasData
{
    public static function execute(int $id_ubi): array
    {
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = $CentroDlRepository->findById($id_ubi);

        return [
            'id_ubi' => $id_ubi,
            'nombre_ubi' => $oCentro?->getNombre_ubi() ?? '',
            'num_habit_indiv' => $oCentro?->getNum_habit_indiv() ?? '',
            'plazas' => $oCentro?->getPlazas() ?? '',
            'sede' => $oCentro?->isSede() ?? false,
        ];
    }
}

